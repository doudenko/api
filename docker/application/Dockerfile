FROM php:8.4-fpm-alpine

USER $USER_ID:$GROUP_ID

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

RUN apk add --no-cache bash linux-headers && \
	apk add --no-cache --update --virtual .build-dependencies $PHPIZE_DEPS && \
	pecl install xdebug && \
	docker-php-ext-enable xdebug && \
	pecl clear-cache && \
	apk del .build-dependencies && \
    rm -rf /tmp/pear

WORKDIR /var/www

ENTRYPOINT ["php-fpm"]
