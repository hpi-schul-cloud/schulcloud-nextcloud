#!/bin/sh

CUSTOM_APPS_PATH="/usr/nextcloud/custom_apps/"

check_environment() {
  # Check if configure needs to run
  if [ ! -f "/var/www/html/executed" ]; then
    echo "Initial Setup. Configuration will run."
  elif [ "$RUN_CONFIGURATION" = True ]; then
    echo "RUN_CONFIGURATION variable set to true. Configuration will run."
  else
    echo "Configuration script already run. Nothing to do."
    exit 0
  fi

  # Check variables
  if [ -z "$INSTALL_PLUGINS" ] || [ -z "$ENABLE_PLUGINS" ] || [ -z "$DISABLE_PLUGINS" ] || [ -z "$CONFIG_JSON" ]; then
    echo "Please note: one or more environment variables are undefined"
  fi

  # set php memory limit to default if env is not set
  echo ${PHP_MEMORY_LIMIT:=512M}
}

# Clone external git plugins
external_plugins() {
  if [ -n "$EXTERNAL_GIT_PLUGINS" ]; then
    rm -rf /usr/nextcloud/external_plugins
    mkdir -p /usr/nextcloud/external_plugins
    cd /usr/nextcloud/external_plugins
    for i in $EXTERNAL_GIT_PLUGINS; do
      url=$(echo "$i" | grep -oP '.+.git(?=\:)')
      directory_name=$(echo "$i" | grep -oP '([^\/]+)(?=\.git)')
      version=$(echo "$i" | grep -oP '([^\:]+)$')
      plugin_name=$(echo "$i" | grep -oP '(?<=\.git:)([^:]+)')
      echo "Cloning $url with version $version"
      git -c advice.detachedHead=false clone -b $version $url
      echo "Renaming $directory_name to $plugin_name"
      mv $directory_name $plugin_name
    done
    cd /var/www/html/
  fi
}

# Check if nextcloud is available to install plugins
waiting_for_nextcloud() {
  while true; do
    sudo -u www-data PHP_MEMORY_LIMIT=$PHP_MEMORY_LIMIT php occ status | grep "installed: true"
    if [ $? -eq 0 ]; then
      echo "Nextcloud is reachable"
      break
    fi
    echo "Nextcloud is not ready. Try again configure in 5 secounds..."
    sleep 5
  done
}

# installs, enables and disables given plugins
manage_plugins() {
  for i in $INSTALL_PLUGINS; do
    sudo -u www-data PHP_MEMORY_LIMIT=$PHP_MEMORY_LIMIT php occ app:install $i
  done

  for i in $ENABLE_PLUGINS; do
    sudo -u www-data PHP_MEMORY_LIMIT=$PHP_MEMORY_LIMIT php occ app:enable $i
  done

  for i in $DISABLE_PLUGINS; do
    sudo -u www-data PHP_MEMORY_LIMIT=$PHP_MEMORY_LIMIT php occ app:disable $i
  done
}

copy_custom_plugins() {
  cp -R /usr/nextcloud/external_plugins/. /var/www/html/custom_apps/
}

import_config() {
  if [ -n "$CONFIG_JSON" ]; then
    echo "$CONFIG_JSON" > ./tmp
    sudo -u www-data PHP_MEMORY_LIMIT=$PHP_MEMORY_LIMIT php occ config:import ./tmp
    rm ./tmp
  else
    echo "No configuration will we be imported. See CONFIG_JSON env."
  fi
}

modify_htaccess() {
  if [ "$DISABLE_USER_SETTINGS" = True ]; then
    grep -qxF '############ CUSTOM_HTACCESS ############' .htaccess || cat /usr/nextcloud/.custom_htaccess >> /var/www/html/.htaccess
    echo "Custom htaccess imported and user settings redirect applied."
  fi
}

modify_htaccess() {
  if [ "$DISABLE_USER_SETTINGS" = True ]; then
    grep -qxF '############ CUSTOM_HTACCESS ############' .htaccess || cat /usr/nextcloud/.custom_htaccess >> /var/www/html/.htaccess
    echo "Custom htaccess imported and user settings redirect applied."
  else
    sudo -u www-data PHP_MEMORY_LIMIT=$PHP_MEMORY_LIMIT php occ maintenance:update:htaccess
  fi
}

######
# main
######
check_environment
echo "Start configuration script..."
external_plugins
waiting_for_nextcloud
# Copy customs plugins to nextcloud after installation, because of overwriting
copy_custom_plugins
manage_plugins
import_config
modify_htaccess
echo "The configuration script was executed" > /var/www/html/executed
