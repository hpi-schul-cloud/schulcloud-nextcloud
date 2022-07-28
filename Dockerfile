# syntax=docker/dockerfile:1
FROM nextcloud:24.0.3 AS base

USER root

RUN apt-get update && apt-get install -y sudo git p7zip p7zip-full \
    && pecl install rar \
    && echo extension=rar.so >> /usr/local/etc/php/conf.d/docker-php-ext-rar.ini

ENV NEXTCLOUD_UPDATE=1

FROM base AS production

COPY ./src/configure.sh /usr/nextcloud/configure.sh
RUN chmod +x /usr/nextcloud/configure.sh

FROM base AS development

RUN apt-get install -y supervisor \
    && rm -rf /var/lib/apt/lists/* \
    && mkdir /var/log/supervisord /var/run/supervisord

COPY ./src /usr/nextcloud
# for mounting
RUN mkdir /var/www/html/custom_apps/
RUN sudo chown -R www-data /var/www/html/custom_apps/

CMD ["/usr/bin/supervisord", "-c", "/usr/nextcloud/supervisord.conf"]
