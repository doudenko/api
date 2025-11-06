FROM php:8.4-fpm-alpine

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

RUN apk add --no-cache bash linux-headers && \
	apk add --no-cache --update --virtual .build-dependencies $PHPIZE_DEPS && \
	pecl install xdebug && \
	docker-php-ext-enable xdebug && \
	pecl clear-cache && \
	apk del .build-dependencies && \
    rm -rf /tmp/pear

USER $UID:$GID

WORKDIR /var/www

EXPOSE $PHP_PORT:9000/tcp

ENTRYPOINT ["php-fpm"]
