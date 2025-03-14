# syntax=docker/dockerfile:1
FROM nextcloud:29.0.1 AS base

USER root

RUN apt-get update && apt-get install -y sudo git p7zip p7zip-full libmagickcore-6.q16-6-extra wget 

RUN git clone https://github.com/remicollet/php-rar.git \
    && cd php-rar && git checkout 02331ca \
    && phpize && ./configure && make && make install \
    && echo extension=rar.so >> /usr/local/etc/php/conf.d/docker-php-ext-rar.ini

# Delete uneeded php extentions    
# php
RUN rm -rf /usr/local/etc/php/conf.d/docker-php-ext-ftp.ini
RUN rm -rf /usr/local/etc/php/conf.d/docker-php-ext-pdo_mysql.ini
# RUN rm -rf /usr/local/etc/php/conf.d/docker-php-ext-redis.ini
RUN rm -rf /usr/local/etc/php/conf.d/docker-php-ext-ldap.ini
RUN rm -rf /usr/local/etc/php/conf.d/docker-php-ext-gmp.ini
RUN rm -rf /usr/local/etc/php/conf.d/docker-php-ext-sysvsem.ini
RUN rm -rf /usr/local/etc/php/conf.d/docker-php-ext-bcmath.ini

# apache2 
# another way is a2dismod mod_name -f
RUN rm -rf /etc/apache2/mods-enabled/status.load
RUN rm -rf /etc/apache2/mods-enabled/status.conf
RUN rm -rf /etc/apache2/mods-enabled/autoindex.conf
RUN rm -rf /etc/apache2/mods-enabled/autoindex.load
RUN rm -rf /usr/lib/apache2/modules/mod_autindex.so
RUN rm -rf /usr/lib/apache2/modules/mod_status.so

ENV NEXTCLOUD_UPDATE=1

FROM base AS production

COPY ./src/configure.sh /usr/nextcloud/configure.sh
RUN chmod +x /usr/nextcloud/configure.sh

FROM base AS development

RUN apt-get install -y supervisor \
    && rm -rf /var/lib/apt/lists/* \
    && mkdir /var/log/supervisord /var/run/supervisord

# update curl to address CVE-2023-38545
RUN apt-get update && apt-get upgrade curl -y

COPY ./src /usr/nextcloud
# for mounting
RUN mkdir /var/www/html/custom_apps/ \
    && sudo chown -R www-data /var/www/html/custom_apps/

CMD ["/usr/bin/supervisord", "-c", "/usr/nextcloud/supervisord.conf"]
