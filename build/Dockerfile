FROM php:7.2-fpm
ENV PHPREDIS_VERSION 4.0.2
RUN apt-get update \
   && apt-get install git libicu-dev g++ libpng-dev wkhtmltopdf xvfb -y \
   && docker-php-ext-install intl \
   && mkdir -p /usr/src/php/ext/redis \
   && curl -L https://github.com/phpredis/phpredis/archive/$PHPREDIS_VERSION.tar.gz | tar xvz -C /usr/src/php/ext/redis --strip 1 \
   && echo 'redis' >> /usr/src/php-available-exts \
   && docker-php-ext-install redis \
   && docker-php-ext-install pdo pdo_mysql \
   && docker-php-ext-install gd mbstring \
   && printf '#!/bin/bash\nxvfb-run -a --server-args="-screen 0, 1024x768x24" /usr/bin/wkhtmltopdf -q $*' > /usr/bin/wkhtmltopdf.sh \
   && chmod a+x /usr/bin/wkhtmltopdf.sh \
   && ln -s /usr/bin/wkhtmltopdf.sh /usr/local/bin/wkhtmltopdf \
   && apt-get autoremove --purge -y \
   && apt-get autoclean