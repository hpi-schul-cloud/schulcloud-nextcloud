#!/bin/sh

if [ -f "/usr/nextcloud/executed" ]; then
  echo "Configuration script already run. Nothing to do."
  exit 0
fi

echo "Start configuration script..."

for i in $INSTALL_PLUGINS; do
  sudo -u www-data PHP_MEMORY_LIMIT=512M php occ app:install $i
  echo "Plugins were installed"
done

for i in $ENABLE_PLUGINS; do
  sudo -u www-data PHP_MEMORY_LIMIT=512M php occ app:enable $i
  echo "Plugins were enabled"
done

for i in $DISABLE_PLUGINS; do
  sudo -u www-data PHP_MEMORY_LIMIT=512M php occ app:disable $i
  echo "Plugins were disabled"
done

echo $CONFIG_JSON > ./tmp
sudo -u www-data PHP_MEMORY_LIMIT=512M php occ config:import ./tmp
rm ./tmp

sudo -u www-data PHP_MEMORY_LIMIT=512M php occ app:update

echo 'The configuration script was executed' > /usr/nextcloud/executed

echo "Done"
