#!/bin/bash

project_dir=/home/gttest/projects/score

cd $project_dir

git reset --hard

if /usr/bin/git pull; then
  echo "sergej hat alle richtig gemacht"
else
   echo "sergej ist schuld"
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
sudo chmod -R 777 /home/gttest/projects/score/storage
php artisan cache:clear
php artisan config:cache
php artisan queue:restart
php artisan up
