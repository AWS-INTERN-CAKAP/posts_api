#!/bin/bash

# Run migrations & seeders
php artisan migrate --force
php artisan db:seed --force

# Start Apache in the foreground
apache2-foreground
