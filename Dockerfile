FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    git unzip libpq-dev \
  && docker-php-ext-install pdo pdo_pgsql \
  && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

# Make sure var exists (Symfony cache/logs)
RUN mkdir -p var && chmod -R 777 var

# Provide a dummy secret ONLY for build-time console calls (runtime will override via env vars)
ENV APP_ENV=prod
ENV APP_SECRET=build_dummy_secret

# IMPORTANT: avoid symfony/flex auto-scripts during build
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Optional: pre-warm cache (won’t break build if it fails)
RUN php bin/console cache:clear --env=prod || true

CMD php -S 0.0.0.0:${PORT:-8000} -t public public/index.php
