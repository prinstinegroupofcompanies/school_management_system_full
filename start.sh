#!/bin/bash

# Exit on any error
set -e

# Create necessary directories
mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache
mkdir -p storage/logs

# Set proper permissions
chmod -R 775 storage bootstrap/cache

# Generate application key if not set
if [ -z "$APP_KEY" ]; then
    echo "Generating application key..."
    php artisan key:generate --force
fi

# Clear and cache configuration
echo "Caching configuration..."
php artisan config:clear || true
php artisan config:cache || true

# Clear and cache routes
echo "Caching routes..."
php artisan route:clear || true
php artisan route:cache || true

# Clear and cache views
echo "Caching views..."
php artisan view:clear || true
php artisan view:cache || true

# Run migrations (ignore errors if database is not ready)
echo "Running migrations..."
php artisan migrate --force || echo "Migration failed, continuing..."

# Seed database if needed (ignore errors)
echo "Seeding database..."
php artisan db:seed --force || echo "Database seeding failed, continuing..."

# Create storage link
echo "Creating storage link..."
php artisan storage:link || echo "Storage link already exists or failed"

# Start the application
echo "Starting Laravel application..."
php artisan serve --host=0.0.0.0 --port=$PORT
