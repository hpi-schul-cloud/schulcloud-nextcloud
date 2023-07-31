#!/bin/sh

isThemingEnabled=False
OCC_COMMAND="sudo -E -u www-data PHP_MEMORY_LIMIT=$PHP_MEMORY_LIMIT php occ "
EXTERNAL_PLUGINS_PATH=/usr/nextcloud/external_plugins

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

  echo "$ENABLE_PLUGINS" | grep -q "theming"
  if [ $? = 1 ]; then
    echo "Theming app is not enabled. Please enable it to apply themes."
  else
    echo "Theming will be applied."
    isThemingEnabled=True
  fi

  # set php memory limit to default if env is not set
  echo ${PHP_MEMORY_LIMIT:=512M}
}

# Clone external git plugins
git_external_plugins() {
  if [ -n "$EXTERNAL_GIT_PLUGINS" ]; then
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
  fi
}

archive_external_plugins() {
  if [ -n "$EXTERNAL_ARCHIVE_PLUGINS" ]; then
    for i in $EXTERNAL_ARCHIVE_PLUGINS; do
      url=$(echo "$i" | grep -oP '.+.tar.gz(?=\:)')
      file_name=$(echo "$i" | grep -oP '(?<=\/)([^\/]+)(?=\:)')
      echo "Downloading $url"
      wget -O "$file_name" "$url"
      tar -xzf "$file_name"
    done
    rm -f ./*.tar.gz
  fi
}

handle_external_plugins() {
  rm -rf "$EXTERNAL_PLUGINS_PATH"
  mkdir -p "$EXTERNAL_PLUGINS_PATH"
  cd "$EXTERNAL_PLUGINS_PATH"
  git_external_plugins
  archive_external_plugins
  cd /var/www/html/
}

# Check if nextcloud is available to install plugins
waiting_for_nextcloud() {
  while true; do
    ${OCC_COMMAND} status | grep -q "installed: true"
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
    ${OCC_COMMAND} app:install $i
  done

  for i in $ENABLE_PLUGINS; do
    ${OCC_COMMAND} app:enable $i
  done

  for i in $DISABLE_PLUGINS; do
    ${OCC_COMMAND} app:disable $i
  done
}

copy_custom_plugins() {
  cp -R /usr/nextcloud/external_plugins/. /var/www/html/custom_apps/
  chown -R www-data /var/www/html/custom_apps/
  echo "Copied custom plugins and changed owner to www-data."
}

import_config() {
  if [ -n "$CONFIG_JSON" ]; then
    echo "$CONFIG_JSON" > ./tmp
    ${OCC_COMMAND} config:import ./tmp
    rm ./tmp
  else
    echo "No configuration will we be imported. See CONFIG_JSON env."
  fi
}

apply_theme() {
  THEMING_COMMAND="${OCC_COMMAND} theming:config"
  if [ -n "$THEMING_NAME" ]; then
    ${THEMING_COMMAND} name "$THEMING_NAME"
    echo "Theming: name was applied"
  fi
  if [ -n "$THEMING_URL" ]; then
    ${THEMING_COMMAND} url "$THEMING_URL"
    echo "Theming: url was applied"
  fi
  if [ -n "$THEMING_SLOGAN" ]; then
      ${THEMING_COMMAND} slogan "$THEMING_SLOGAN"
      echo "Theming: slogan was applied"
  fi
  if [ -n "$THEMING_COLOR" ]; then
      ${THEMING_COMMAND} color "$THEMING_COLOR"
      echo "Theming: color was applied"
  fi
  if [ -n "$THEMING_LOGO_URL" ]; then
      filename=$(basename "$THEMING_LOGO_URL")
      wget -O "$filename" "$THEMING_LOGO_URL"
      current_dir=`pwd`
      ${THEMING_COMMAND} logo ${current_dir}/${filename}
      echo "Theming: logo was applied"
  fi
}

run_post_config_command() {
  if [ -n "$POST_CONFIG_COMMAND" ]; then
    echo "Running post config command"
    echo $POST_CONFIG_COMMAND | bash # eval "$POST_CONFIG_COMMAND"
  fi
}

######
# main
######
check_environment
echo "Start configuration script..."
handle_external_plugins
waiting_for_nextcloud
# Copy customs plugins to nextcloud after installation, because of overwriting
copy_custom_plugins
manage_plugins
import_config
if [ "$isThemingEnabled" = True ]; then
  apply_theme
fi
run_post_config_command
echo "Configuration script finished successfully!"

echo "The configuration script was executed" > /var/www/html/executed
