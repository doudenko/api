FROM php:8.3-fpm-alpine

USER $USER_ID:$GROUP_ID

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

RUN apk add --no-cache bash linux-headers \
	&& apk add --update --no-cache --virtual .build-dependencies $PHPIZE_DEPS \
	&& pecl install xdebug \
	&& docker-php-ext-enable xdebug \
	&& pecl clear-cache \
	&& apk del .build-dependencies

WORKDIR /var/www

EXPOSE $PHP_PORT:9000

ENTRYPOINT ["php-fpm"]
