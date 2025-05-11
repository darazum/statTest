FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    libzip-dev zip unzip git curl \
    autoconf g++ make libssl-dev pkg-config re2c \
    libbrotli-dev \
    && docker-php-ext-install zip

# Swoole
RUN pecl install swoole && docker-php-ext-enable swoole

# Redis
RUN pecl install redis && docker-php-ext-enable redis

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.* ./
RUN composer install --no-dev --prefer-dist

COPY . .

EXPOSE 9501

CMD ["php", "swoole-server.php"]