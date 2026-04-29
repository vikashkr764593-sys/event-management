#!/bin/bash
set -e

# Run database migrations FIRST so tables like 'cache' exist
php artisan migrate --force

# Clear caches
php artisan optimize:clear

# Cache configuration and routes for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Fix permissions since artisan commands might have created files as root
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Start Apache in foreground
exec apache2-foreground
