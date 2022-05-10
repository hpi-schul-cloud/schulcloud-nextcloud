#!/bin/bash
#phpunit has to be installed with composer to the vendor directory

docker-compose -p schulcloud-nextcloud-tests up -d --no-recreate --build

CMD="
while ! [ -f '/usr/nextcloud/executed' ]; do
    echo 'Nextcloud is not fully configured yet. Try again in 10 secounds...'
    sleep 10
done
cd ./custom_apps/schulcloud
composer update
./vendor/bin/phpunit --testdox --do-not-cache-result
"

if [ $# -ge 1 ]; then
    docker exec -u www-data -it schulcloud-nextcloud-tests-nextcloud-1 bash -c "$CMD --testsuite $1"
else
    docker exec -u www-data -it schulcloud-nextcloud-tests-nextcloud-1 bash -c "$CMD"
fi
