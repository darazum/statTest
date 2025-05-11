FROM php:8.2-cli

# Установка системных зависимостей
RUN apt-get update && apt-get install -y \
    libzip-dev zip unzip git curl \
    autoconf g++ make libssl-dev pkg-config re2c && \
    docker-php-ext-install zip

# Установка Swoole
RUN pecl install swoole && docker-php-ext-enable swoole

# Установка Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Копирование composer и установка зависимостей
COPY composer.* ./
RUN composer install --no-dev --prefer-dist || true

# Копирование исходников
COPY . .

EXPOSE 9501

CMD ["php", "swoole-server.php"]