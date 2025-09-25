#!/bin/bash

# School Management System Deployment Script for Render

set -e  # Exit on any error

echo "Starting deployment process..."

# Install dependencies
echo "Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

# Generate application key
echo "Generating application key..."
php artisan key:generate --force

# Wait for database to be ready
echo "Waiting for database connection..."
sleep 20

# Test database connection with retry
echo "Testing database connection..."
for i in {1..5}; do
    if php artisan tinker --execute="try { DB::connection()->getPdo(); echo 'Database connected successfully'; } catch (Exception \$e) { echo 'Database connection failed: ' . \$e->getMessage(); exit(1); }"; then
        echo "Database connection successful"
        break
    else
        echo "Database connection attempt $i failed, retrying..."
        sleep 5
    fi
done

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

# Set proper permissions
echo "Setting permissions..."
chmod -R 755 storage bootstrap/cache

echo "Deployment completed successfully!"
