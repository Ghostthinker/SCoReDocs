#!/bin/bash

project_dir=/var/www/demo.score-docs.de

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

composer install

npm install

php artisan backup:clean
php artisan backup:run --only-db

npm run prod

php artisan down
php artisan migrate

php artisan cache:clear
php artisan queue:restart
php artisan up

