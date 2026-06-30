FROM php:8.4-fpm

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        bash \
        git \
        libicu-dev \
        libpq-dev \
        librabbitmq-dev \
        postgresql-client \
    && pecl config-set download_buffer_size 33554432 \
    && pecl channel-update pecl.php.net \
    && pecl install amqp && docker-php-ext-enable amqp \
    && docker-php-ext-install pdo_pgsql pgsql intl \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && rm -rf /var/lib/apt/lists/*


COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
