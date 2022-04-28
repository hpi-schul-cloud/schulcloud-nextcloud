#!/bin/sh

if [ -f "/usr/nextcloud/executed" ]; then
  echo "Configuration script already run. Nothing to do."
  exit 0
fi

while true; do
  sudo -u www-data PHP_MEMORY_LIMIT=512M php occ status | grep "installed: true"
  if [ $? -eq 0 ]; then
    echo "Nextcloud is reachable"
    break
  fi
  echo "Nextcloud is not ready. Try again configure in 5 secounds..."
  sleep 5
done

echo "Start configuration script..."

# Copy customs apps to nextcloud after installation, because of overwriting
cp -R /usr/nextcloud/custom_apps/. /var/www/html/custom_apps/

for i in $INSTALL_PLUGINS; do
  sudo -u www-data PHP_MEMORY_LIMIT=512M php occ app:install $i
done

for i in $ENABLE_PLUGINS; do
  sudo -u www-data PHP_MEMORY_LIMIT=512M php occ app:enable $i
done

for i in $DISABLE_PLUGINS; do
  sudo -u www-data PHP_MEMORY_LIMIT=512M php occ app:disable $i
done

echo $CONFIG_JSON >./tmp
sudo -u www-data PHP_MEMORY_LIMIT=512M php occ config:import ./tmp
rm ./tmp

sudo -u www-data PHP_MEMORY_LIMIT=512M php occ upgrade

echo 'The configuration script was executed' >>/usr/nextcloud/executed

echo "Done"
