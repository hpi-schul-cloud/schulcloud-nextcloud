#!/bin/bash

docker create \
 -p 8080:80 \
 -e MYSQL_HOST=mysql \
 -e MYSQL_DATABASE=nextcloud \
 -e MYSQL_USER=nextcloud \
 -e MYSQL_PASSWORD=nextcloud \
 -e NEXTCLOUD_ADMIN_USER=admin \
 -e NEXTCLOUD_ADMIN_PASSWORD=admin \
 --name schulcloud-nextcloud \
 schulcloud/schulcloud-nextcloud/dev:latest