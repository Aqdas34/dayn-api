FROM php:8.2-cli

# System dependencies
RUN apt-get update && apt-get install -y \
    git unzip libpq-dev \
  && docker-php-ext-install pdo pdo_pgsql \
  && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

# Symfony needs writable var directory
RUN mkdir -p var && chmod -R 777 var

# Install dependencies without auto-scripts (prevents dotenv crash)
RUN composer install --no-dev --optimize-autoloader --no-scripts

# 🚀 CREATE FULL .env AT RUNTIME (YOUR VALUES)
CMD sh -c '\
echo "APP_ENV=prod" > .env && \
echo "APP_DEBUG=0" >> .env && \
echo "APP_SECRET=43c40aa8d1017587d75c3e99ba5902d3" >> .env && \
echo "DATABASE_URL=sqlite:///var/data.db" >> .env && \
echo "MESSENGER_TRANSPORT_DSN=doctrine://default?auto_setup=0" >> .env && \
echo "TWILIO_ACCOUNT_SID=ACc62a79ed4d86843429145c13e92dba71" >> .env && \
echo "TWILIO_AUTH_TOKEN=91c2ab363a5ca4d1ddb5225491d03445" >> .env && \
echo "TWILIO_NUMBER=+18076977297" >> .env && \
echo "MAILER_DSN=smtp://noreply@daynapp.com:DaynApp@321@mail.daynapp.com:465?encryption=ssl&auth_mode=login" >> .env && \
echo "MAILER_SENDER_EMAIL_ADDRESS=noreply@daynapp.com" >> .env && \
echo "monnify_CLIENT_ID=MK_TEST_BU918A4GNX" >> .env && \
echo "monnify_CLIENT_SECRET=C427FM0C4PQ3RHZBCBYLBVV7HM4Y098F" >> .env && \
echo "monnify_BASE_URL=https://sandbox.monnify.com" >> .env && \
echo "MONNIFY_CONTRACT_CODE=4090754839" >> .env && \
echo "monnify_ENVIRONMENT=SANDBOX" >> .env && \
php -S 0.0.0.0:${PORT:-8000} -t public public/index.php \
'
