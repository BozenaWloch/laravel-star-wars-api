#!/bin/sh
cd /var/www/html
php artisan migrate --force
env > /etc/environment
