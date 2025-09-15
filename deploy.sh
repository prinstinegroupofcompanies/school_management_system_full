#!/bin/bash

# Render Deployment Script
echo "Starting deployment process..."

# Install dependencies
echo "Installing dependencies..."
composer install --no-dev --optimize-autoloader

# Clear and cache configurations
echo "Caching configurations..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run database migrations
echo "Running database migrations..."
php artisan migrate --force

# Run database seeders for production
echo "Running database seeders..."
php artisan db:seed --force

echo "Deployment completed successfully!"