# syntax=docker/dockerfile:1
FROM nextcloud:latest

RUN apt-get update && apt-get install -y \
    supervisor sudo \
  && rm -rf /var/lib/apt/lists/* \
  && mkdir /var/log/supervisord /var/run/supervisord

COPY supervisord.conf /

ADD config/import.json /usr/nextcloud/import.json
ADD configure.sh /usr/nextcloud/configure.sh

ENV NEXTCLOUD_UPDATE=1
ENV PHP_MEMORY_LIMIT=512M

CMD ["/usr/bin/supervisord", "-c", "/supervisord.conf"]
