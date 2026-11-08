#!/bin/sh

cp .env.example .env
composer install
php artisan key:generate
php artisan migrate
php artisan storage:link