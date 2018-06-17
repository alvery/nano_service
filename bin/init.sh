#!/bin/sh

composer install --optimize-autoloader

# Clear config cache
php artisan config:clear

# Process all migrations
php artisan migrate
