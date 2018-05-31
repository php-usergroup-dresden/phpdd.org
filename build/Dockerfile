FROM php:7.2-fpm
ENV PHPREDIS_VERSION 4.0.2
RUN apt-get update \
   && apt-get install git libicu-dev g++ -y \
   && docker-php-ext-install intl \
   && mkdir -p /usr/src/php/ext/redis \
   && curl -L https://github.com/phpredis/phpredis/archive/$PHPREDIS_VERSION.tar.gz | tar xvz -C /usr/src/php/ext/redis --strip 1 \
   && echo 'redis' >> /usr/src/php-available-exts \
   && docker-php-ext-install redis \
   && docker-php-ext-install pdo pdo_mysql \
   && apt-get autoremove --purge -y \
   && apt-get autoclean