FROM composer:2 AS composer_stage

FROM php:8.2-cli

# Установка зависимостей ОС
RUN apt-get update && apt-get install -y \
    libzip-dev zip unzip git curl \
    autoconf g++ make libssl-dev pkg-config re2c \
    libbrotli-dev \
    && docker-php-ext-install zip

RUN apt-get update && apt-get install -y \
    bash \
    curl \
    procps \
    iproute2 \
    net-tools \
    htop

# Установка PHP-расширений
RUN pecl install swoole && docker-php-ext-enable swoole
RUN pecl install redis && docker-php-ext-enable redis

# ❗ Копируем composer из stage, а не глобального registry
COPY --from=composer_stage /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.* ./
RUN composer install --no-dev --prefer-dist && composer dump-autoload --optimize

COPY . .

RUN date -u +"%Y-%m-%dT%H:%M:%SZ" > /build_time.txt

EXPOSE 9501

CMD ["php", "swoole-server.php"]