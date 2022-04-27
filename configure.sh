#!/bin/sh

echo "Start configuration script..."

sudo -u www-data PHP_MEMORY_LIMIT=512M php occ config:import /usr/nextcloud/import.json
sudo -u www-data PHP_MEMORY_LIMIT=512M php occ app:install sociallogin || true
sudo -u www-data PHP_MEMORY_LIMIT=512M php occ app:install groupfolders || true

DIR="/var/www/html/core/skeleton"
if [ -d "$DIR" ]; then
  rm -r /var/www/html/core/skeleton
  echo "Removed skeleton"
fi
echo "Done..."

