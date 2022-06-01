#!/bin/bash

cd /var/www/
rm -R html
ln -s /var/www/laravel/public /var/www/html

# beautyful chat
cd /var/www/laravel
composer install
git submodule sync --recursive
git submodule update --init --recursive

cd ./resources/vue-beautiful-chat
#cd /var/www/laravel/resources/vue-beautiful-chat
npm install

cd /var/www/laravel/
composer install

# give file access
chmod 777 -R storage/


php artisan migrate
php artisan cache:clear
php artisan queue:restart
php artisan db:seed

#copy gitpod debugger config
cp -a ./gitpod/docker-php-ext-xdebug.ini /usr/local/etc/php/conf.d/

exit