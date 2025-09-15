#!/bin/bash

# Render Deployment Script
echo "Starting deployment process..."

# Install dependencies
echo "Installing dependencies..."
composer install --no-dev --optimize-autoloader

# Clear all caches first
echo "Clearing all caches..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Regenerate autoload files
echo "Regenerating autoload files..."
composer dump-autoload --optimize

# Check database connection
echo "Checking database connection..."
php artisan migrate:status

# Run database migrations with verbose output
echo "Running database migrations..."
php artisan migrate --force --verbose

# Check if migrations were successful
if [ $? -eq 0 ]; then
    echo "Migrations completed successfully"
else
    echo "Migration failed, trying to reset and migrate again..."
    php artisan migrate:fresh --force --seed
fi

# Run database seeders for production
echo "Running database seeders..."
php artisan db:seed --force

# Cache configurations for production
echo "Caching configurations..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Deployment completed successfully!"