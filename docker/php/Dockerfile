FROM php:8.3-fpm

RUN set -eux; \
    apt-get update; \
    apt-get install -y --no-install-recommends \
        bash \
        git \
        curl \
        unzip \
        autoconf \
        make \
        g++ \
        pkg-config \
        libicu-dev \
        libpq-dev \
        librabbitmq-dev \
        librabbitmq4 \
        postgresql-client \
        libzip-dev \
        zip \
        php-redis; \
    \
    docker-php-ext-install -j"$(nproc)" \
        intl \
        pdo_pgsql \
        pgsql \
        zip; \
    \
    pecl channel-update pecl.php.net; \
    pecl install amqp-2.1.2; \
    docker-php-ext-enable amqp; \
    \
    rm -rf /var/lib/apt/lists/* /tmp/pear

RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/html