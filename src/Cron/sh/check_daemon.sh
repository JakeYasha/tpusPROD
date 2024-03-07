#!/bin/bash

param=$(pgrep -f "yml_daemon")
if [[ -z "${param// }" ]]
then
/usr/local/php-fpm/bin/php /var/www/sites/tovaryplus.ru/src/Cron/php/yml_daemon.php &
fi
