#!/bin/bash

project_dir=/var/www/www.score-docs.de

cd $project_dir

chmod -R 775 storage/logs

php artisan config:clear

git reset --hard

if /usr/bin/git pull; then
  echo "checkout successful"
else
   echo "checkout failed"
   exit 42
fi

git submodule sync --recursive
git submodule update --init --recursive
cd ./resources/vue-beautiful-chat
npm install

cd $project_dir

composer install --optimize-autoloader --no-dev

npm install

php artisan backup:clean
php artisan backup:run --only-db

npm run prod

php artisan config:cache
php artisan route:cache
php artisan view:cache

php artisan migrate
php artisan cache:clear
php artisan queue:restart

