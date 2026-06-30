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
        postgresql-client; \
    \
    docker-php-ext-install -j"$(nproc)" \
        intl \
        pdo_pgsql \
        pgsql; \
    \
    pecl channel-update pecl.php.net; \
    pecl install amqp; \
    docker-php-ext-enable amqp; \
    \
    pecl install redis; \
    docker-php-ext-enable redis; \
    \
    apt-get clean; \
    rm -rf /var/lib/apt/lists/* /tmp/pear

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html