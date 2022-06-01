#!/bin/bash

composer install
#npm install
php artisan migrate
php artisan cache:clear
