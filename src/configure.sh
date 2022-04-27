#!/bin/sh

if [ -f "/usr/nextcloud/executed" ]; then
  echo "Configuration script already run. Nothing to do."
  exit 0
fi

echo "Start configuration script..."

ROOT="/var/www/html"

echo "Installing custom apps..."
cp -r /usr/nextcloud/custom_apps $ROOT
sudo -u www-data PHP_MEMORY_LIMIT=512M php occ config:import /usr/nextcloud/import.json
sudo -u www-data PHP_MEMORY_LIMIT=512M php occ app:install sociallogin || true
sudo -u www-data PHP_MEMORY_LIMIT=512M php occ app:install groupfolders || true
echo "Custom apps installed"

if [ -d "$ROOT/core/skeleton" ]; then
  echo "Removing default files..."
  rm -r $ROOT/core/skeleton
  echo "Default files removed"
fi

echo 'The configuration script was executed' > /usr/nextcloud/executed

echo "Done"
