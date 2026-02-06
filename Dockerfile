FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    git unzip libpq-dev \
  && docker-php-ext-install pdo pdo_pgsql \
  && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

# Symfony needs this folder writable
RUN mkdir -p var && chmod -R 777 var

# Build-time env so Symfony doesn't default to dev if anything runs
ENV APP_ENV=prod
ENV APP_DEBUG=0
ENV APP_SECRET=build_dummy_secret

# ✅ IMPORTANT: stop Symfony Flex auto-scripts (cache:clear) during build
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Optional: warm cache, but don't fail build if it errors
RUN php bin/console cache:clear --env=prod || true

CMD php -S 0.0.0.0:${PORT:-8000} -t public public/index.php
