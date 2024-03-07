#!/bin/bash

# Чистим общий каталог от старых файлов
cd /var/www/sites/tovaryplus.ru/update
rm *.txt -f
rm full -f

cd /var/www/sites/tovaryplus.ru/update/utf8
rm *.txt -f

cd /var/www/sites/tovaryplus.ru/update/ratiss_image
rm *.* -f
for i in /var/www/sites/tovaryplus.ru/update/ratiss_image/*; do rm -f $i; done


# Переходим в каталог с разбивкой данных по сервисам
cd /var/www/sites/tovaryplus.ru/update/service_update

for D in `find . -maxdepth 1 ! -path . -type d`
do
    cd /var/www/sites/tovaryplus.ru/update/service_update/$D

	for file in *.txt.gz; do
		if [[ -f $file ]]; then
			echo "gunzip $file ..."
			gunzip -f $file
		fi
	done

	echo /var/www/sites/tovaryplus.ru/update/service_update/$D
	for file in *.txt; do
		if [[ -f $file ]]; then
			echo "iconv $file ..."
			iconv -f WINDOWS-1251 -t UTF-8 < $file > /var/www/sites/tovaryplus.ru/update/service_update/$D/utf8/$file
			cat /var/www/sites/tovaryplus.ru/update/service_update/$D/utf8/$file >> /var/www/sites/tovaryplus.ru/update/utf8/$file
		fi
	done
	
	for i in /var/www/sites/tovaryplus.ru/update/service_update/$D/ratiss_image/*; do 
		if [[ -f $i ]]; then
			cp $i /var/www/sites/tovaryplus.ru/update/ratiss_image;
		fi
	done
	
done

echo "подготовка завершена"
echo "запускаем обновление базы данных..."

/usr/local/php-fpm/bin/php /var/www/sites/tovaryplus.ru/src/Cron/php/daily.php
/usr/local/php-fpm/bin/php /var/www/sites/tovaryplus.ru/src/Cron/php/daily2.php

cd /var/www/sites/tovaryplus.ru/update/service_update
for D in `find . -maxdepth 1 ! -path . -type d`
do
	cd /var/www/sites/tovaryplus.ru/update/service_update/$D
	rm *.txt -f
	rm full -f

	cd /var/www/sites/tovaryplus.ru/update/service_update/$D/utf8
	rm *.txt -f

	cd /var/www/sites/tovaryplus.ru/update/service_update/$D/ratiss_image
	rm *.* -f

	for i in /var/www/sites/tovaryplus.ru/update/service_update/$D/ratiss_image/*; do rm -f $i; done
done

# Чистим общий каталог от файлов
#cd /var/www/sites/tovaryplus.ru/update
#rm *.txt -f
#rm full -f
#
#cd /var/www/sites/tovaryplus.ru/update/utf8
#rm *.txt -f
#
#cd /var/www/sites/tovaryplus.ru/update/ratiss_image
#rm *.* -f
#for i in /var/www/sites/tovaryplus.ru/update/ratiss_image/*; do rm -f $i; done


#_token=$(curl -s -X POST https://xlorspace.ru/bot/ -d action="auth" -d auth_key="b1824b0c-34f4-4f15-af8f-20a2ad08ea62")
#curl -s -X POST https://xlorspace.ru/bot/ -d action="send" -d token=$_token -d message="Обновление завершено"