FROM php:7.4-cli-alpine

ENV XDEBUGINI_PATH=/usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer --version

RUN apk update \
    && apk --no-cache add pcre-dev ${PHPIZE_DEPS} \
    && pecl install xdebug && docker-php-ext-enable xdebug \
    && echo "xdebug.remote_enable=1" >> $XDEBUGINI_PATH \
    && echo "xdebug.remote_autostart=1" >> $XDEBUGINI_PATH \
    && echo "xdebug.remote_host=192.168.10.1" >> $XDEBUGINI_PATH \
    && echo "xdebug.idekey=PHPSTORM" >> $XDEBUGINI_PATH \
    && apk del pcre-dev ${PHPIZE_DEPS}

RUN docker-php-ext-install bcmath