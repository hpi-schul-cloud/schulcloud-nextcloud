#!/bin/sh

CUSTOM_APPS_PATH="/usr/nextcloud/custom_apps/"

# Check if configure needs to run
if [ ! -f "/var/www/html/executed" ]; then
  echo "Initial Setup. Configuration will run."
elif [ "$RUN_CONFIGURATION" = True ]; then
  echo "RUN_CONFIGURATION variabel set to True. Configuration will run."
else
  echo "Configuration script already run. Nothing to do."
  exit 0
fi

# Check variables
if [ -z "$INSTALL_PLUGINS" ] || [ -z "$ENABLE_PLUGINS" ] || [ -z "$DISABLE_PLUGINS" ] || [ -z "$CONFIG_JSON" ]; then
  echo "One or more environment variables are undefined"
fi

# Clone external git plugins
if [ -n "$EXTERNAL_GIT_PLUGINS" ]; then
  rm -rf /usr/nextcloud/external_plugins
  mkdir -p /usr/nextcloud/external_plugins
  cd /usr/nextcloud/external_plugins
  for i in $EXTERNAL_GIT_PLUGINS; do
    echo "Cloning $i"
    git clone $i
  done
  cd /var/www/html/
fi

# Check if nextcloud is available to install plugins
while true; do
  sudo -u www-data PHP_MEMORY_LIMIT=512M php occ status | grep "installed: true"
  if [ $? -eq 0 ]; then
    echo "Nextcloud is reachable"
    break
  fi
  echo "Nextcloud is not ready. Try again configure in 5 secounds..."
  sleep 5
done

# Copy customs apps to nextcloud after installation, because of overwriting
cp -R /usr/nextcloud/custom_apps/. /var/www/html/custom_apps/
cp -R /usr/nextcloud/external_plugins/. /var/www/html/custom_apps/

echo "Start configuration script..."

for i in $INSTALL_PLUGINS; do
  sudo -u www-data PHP_MEMORY_LIMIT=512M php occ app:install $i
done

for i in $ENABLE_PLUGINS; do
  sudo -u www-data PHP_MEMORY_LIMIT=512M php occ app:enable $i
done

for i in $DISABLE_PLUGINS; do
  sudo -u www-data PHP_MEMORY_LIMIT=512M php occ app:disable $i
done

if [ -n "$CONFIG_JSON" ]; then
  echo "$CONFIG_JSON" >./tmp
  sudo -u www-data PHP_MEMORY_LIMIT=512M php occ config:import ./tmp
  rm ./tmp
else
  echo "No configuration will we be imported. See CONFIG_JSON env."
fi

echo "The configuration script was executed" >> /var/www/html/executed

echo "Done"
