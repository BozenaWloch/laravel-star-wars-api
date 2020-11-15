###############################
# Stage: dependencies (Composer)
###############################
FROM composer:latest AS dependencies

COPY composer.* ./

RUN composer install \
    --ignore-platform-reqs \
    --no-ansi \
    --no-interaction \
    --no-progress \
    --no-scripts \
    --optimize-autoloader \
    --prefer-dist

###############################
# Stage: PHP
###############################
FROM php:7.4.11-apache AS php

RUN apt-get update

RUN apt-get install -y libzip-dev libpng-dev libfreetype6-dev libjpeg62-turbo-dev libcurl4-openssl-dev pkg-config libssl-dev openssl libjpeg-dev supervisor cron git zip libgmp-dev libmhash-dev libmcrypt-dev libicu-dev gnupg2
RUN docker-php-ext-install pdo_mysql zip

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-configure gmp \
    && docker-php-ext-install gmp intl
RUN docker-php-ext-install gd

FROM php AS application

WORKDIR /var/www/html

# Application code & dependencies
COPY . ./

COPY --from=dependencies /app/vendor ./vendor
COPY --from=dependencies /usr/bin/composer /usr/bin/

###############################
# Stage: serve
###############################
FROM application AS serve

RUN apt-get update
RUN apt-get install -y iproute2 unzip git redis-tools

RUN a2enmod rewrite headers

HEALTHCHECK --interval=5s --timeout=1s \
  CMD curl --user-agent "Docker HEALTHCHECK" --fail http://localhost || exit 1

COPY ./server/supervisord.conf /etc/supervisor/conf.d/
COPY ./server/000-default.conf /etc/apache2/sites-available/
COPY ./server/php-ini-overrides.ini ./server/opcache.ini ${PHP_INI_DIR}/conf.d/
COPY ./server/openssl.cnf /etc/ssl/openssl.cnf

# Store writable
RUN chmod +x /var/www/html/migrate.sh \
    && chmod -R 777 /var/www/html/storage
RUN mkdir -p /var/www/html/storage/logs
RUN chown -R www-data:www-data /var/www/html

CMD ["/usr/bin/supervisord"]
