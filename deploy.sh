#!/bin/bash

# School Management System Deployment Script for Render

echo "Starting deployment process..."

# Install dependencies
echo "Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

# Generate application key if not set
echo "Generating application key..."
php artisan key:generate --force

# Wait for database to be ready
echo "Waiting for database connection..."
sleep 10

# Run migrations
echo "Running database migrations..."
php artisan migrate --force

# Seed the database
echo "Seeding database..."
php artisan db:seed --force

# Clear and cache configuration
echo "Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Create storage link
echo "Creating storage link..."
php artisan storage:link

echo "Deployment completed successfully!"
