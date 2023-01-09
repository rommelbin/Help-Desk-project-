FROM alpine/git:v2.32.0 as wait
ADD https://github.com/ufoscout/docker-compose-wait/releases/download/2.8.0/wait /wait

FROM composer as composer
WORKDIR /app
COPY composer.json .
COPY composer.lock .
RUN composer install --no-interaction --no-progress --no-autoloader --dev
COPY . .
RUN composer dump-autoload

FROM php:7.4.25-fpm-alpine
RUN echo "UTC" > /etc/timezone
COPY --from=wait /wait /wait
RUN chmod +x /wait
RUN set -ex \
    && apk --no-cache add postgresql-dev \
    && docker-php-ext-install pdo_pgsql json
WORKDIR /app
COPY --from=composer /app .
CMD /wait \
    && ./artisan migrate --force --seed \
    && ./artisan storage:link \
    && php -S 0.0.0.0:80 -t public
