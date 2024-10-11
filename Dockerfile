FROM php:8.3-fpm-alpine

WORKDIR /app

RUN apk --update upgrade \
    && apk add --no-cache autoconf automake make gcc g++ git bash icu-dev libzip-dev linux-headers

RUN id -u ${USER} &>/dev/null || adduser -D -u ${UID} ${USER}
USER ${USER}

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN pecl install apcu-5.1.23 && pecl install xdebug-3.3.0

RUN docker-php-ext-install -j$(nproc) \
        bcmath \
        opcache \
        intl \
        zip \
        pdo_mysql

RUN docker-php-ext-enable apcu opcache

COPY etc/infrastructure/php/ /usr/local/etc/php/
