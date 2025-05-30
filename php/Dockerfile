# Stage 2: PHP runtime
FROM php:8.3-fpm-alpine AS runtime

RUN apk add --no-cache curl \
 && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
 && chmod +x /usr/local/bin/composer

RUN apk add --no-cache nodejs npm \
 && npm install -g yarn \
 && yarn --version

RUN apk update && apk upgrade \
 && apk add --no-cache \
        postgresql-dev \
        libmemcached-libs \
        zlib \
        libsasl \
        libjpeg-turbo \
        libpng \
        freetype \
        libxml2 \
        libzip \
        icu-libs \
        curl \
 && apk add --no-cache --virtual .build-deps \
        $PHPIZE_DEPS \
        libmemcached-dev \
        zlib-dev \
        cyrus-sasl-dev \
        libjpeg-turbo-dev \
        libpng-dev \
        freetype-dev \
        libxml2-dev \
        libzip-dev \
        icu-dev \
 && docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_pgsql \
        pdo_mysql \
        opcache \
        gd \
        bcmath \
        xml \
        zip \
        intl \
 && pecl install memcached \
 && docker-php-ext-enable memcached \
 && echo "opcache.enable=1" >> /usr/local/etc/php/conf.d/opcache.ini \
 && echo "opcache.memory_consumption=256" >> /usr/local/etc/php/conf.d/opcache.ini \
 && echo "opcache.max_accelerated_files=20000" >> /usr/local/etc/php/conf.d/opcache.ini \
 && apk del .build-deps \
 && rm -rf /var/cache/apk/* /tmp/*

RUN mkdir -p /var/www/app && \
    chown -R www-data:www-data /var/www/app && \
    chmod -R 755 /var/www/app

USER www-data

WORKDIR /var/www/app
COPY --chown=www-data php/ /var/www/app/

EXPOSE 9000

CMD ["php-fpm", "--nodaemonize"]