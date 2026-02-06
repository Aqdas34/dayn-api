FROM php:8.2-cli

# System dependencies + Postgres PDO (safe even if you use SQLite)
RUN apt-get update && apt-get install -y \
    git unzip libpq-dev \
  && docker-php-ext-install pdo pdo_pgsql \
  && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copy app
COPY . .

# Symfony needs var/ writable
RUN mkdir -p var && chmod -R 777 var

# Build-time env (prevents Symfony defaulting to dev if something runs)
ENV APP_ENV=prod
ENV APP_DEBUG=0
ENV APP_SECRET=build_dummy_secret

# Install dependencies WITHOUT running Symfony Flex auto-scripts (cache:clear)
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Optional cache warmup (won't fail build)
RUN php bin/console cache:clear --env=prod || true

# Koyeb provides $PORT at runtime; export env vars for the php process
CMD sh -c 'export APP_ENV="${APP_ENV:-prod}" APP_DEBUG="${APP_DEBUG:-0}" APP_SECRET="${APP_SECRET:-change-me}"; php -S 0.0.0.0:${PORT:-8000} -t public public/index.php'
