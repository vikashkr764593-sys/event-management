#!/bin/bash
set -e

# Clear caches
php artisan optimize:clear

# Cache configuration and routes for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run database migrations
# Force is required to run migrations in production
php artisan migrate --force

# Start Apache in foreground
exec apache2-foreground
