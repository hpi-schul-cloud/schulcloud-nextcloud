# syntax=docker/dockerfile:1
FROM nextcloud:latest AS base

USER root

RUN apt-get update && apt-get install -y sudo

ENV NEXTCLOUD_UPDATE=1

FROM base AS production

COPY ./src/configure.sh /usr/nextcloud/configure.sh
RUN chmod +x /usr/nextcloud/configure.sh
COPY ./src/custom_apps /usr/nextcloud/custom_apps

FROM base AS development

RUN apt-get install -y supervisor \
    && rm -rf /var/lib/apt/lists/* \
    && mkdir /var/log/supervisord /var/run/supervisord

COPY ./src /usr/nextcloud
RUN mkdir /var/www/html/custom_apps/
RUN sudo chown -R www-data /var/www/html/custom_apps/

CMD ["/usr/bin/supervisord", "-c", "/usr/nextcloud/supervisord.conf"]
