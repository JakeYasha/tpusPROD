#!/bin/bash

cd /var/www/sites/tovaryplus.ru/update

for file in *.txt.gz; do
  echo "gunzip $file ..."
  gunzip -f $file
done

for file in *.txt; do
    echo "iconv $file ..."
    iconv -f WINDOWS-1251 -t UTF-8 < $file > utf8/$file
done


echo "подготовка завершена"
echo "запускаем обновление базы данных..."

/usr/local/php-fpm/bin/php /var/www/sites/tovaryplus.ru/src/Cron/php/daily.php
/usr/local/php-fpm/bin/php /var/www/sites/tovaryplus.ru/src/Cron/php/daily2.php

cd /var/www/sites/tovaryplus.ru/update
rm *.txt -f
rm full -f

cd /var/www/sites/tovaryplus.ru/update/utf8
rm *.txt -f

cd /var/www/sites/tovaryplus.ru/update/ratiss_image
rm *.* -f
for i in /var/www/sites/tovaryplus.ru/update/ratiss_image/*; do rm -f $i; done

#_token=$(curl -s -X POST https://xlorspace.ru/bot/ -d action="auth" -d auth_key="b1824b0c-34f4-4f15-af8f-20a2ad08ea62")
#curl -s -X POST https://xlorspace.ru/bot/ -d action="send" -d token=$_token -d message="Обновление завершено"