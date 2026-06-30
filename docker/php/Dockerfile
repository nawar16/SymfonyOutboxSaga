FROM php:8.4-fpm

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        bash \
        git \
        libicu-dev \
        libpq-dev \
        librabbitmq-dev \
        postgresql-client \
    && mkdir -p /tmp/amqp \
    && curl -fsSL https://php.net -o /tmp/amqp.tgz \
    && tar -xf /tmp/amqp.tgz -C /tmp/amqp --strip-components=1 \
    && cd /tmp/amqp && phpize && ./configure && make && make install \
    && docker-php-ext-enable amqp \
    && docker-php-ext-install pdo_pgsql pgsql intl \
    && mkdir -p /tmp/redis \
    && curl -fsSL https://php.net -o /tmp/redis.tgz \
    && tar -xf /tmp/redis.tgz -C /tmp/redis --strip-components=1 \
    && cd /tmp/redis && phpize && ./configure && make && make install \
    && docker-php-ext-enable redis \
    && rm -rf /var/lib/apt/lists/* /tmp/amqp* /tmp/redis*



COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
