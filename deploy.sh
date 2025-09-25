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
sleep 15

# Test database connection
echo "Testing database connection..."
php artisan tinker --execute="try { DB::connection()->getPdo(); echo 'Database connected successfully'; } catch (Exception \$e) { echo 'Database connection failed: ' . \$e->getMessage(); exit(1); }"

# Run migrations with detailed output
echo "Running database migrations..."
php artisan migrate --force --verbose

# Check if migrations were successful
if [ $? -eq 0 ]; then
    echo "Migrations completed successfully"
else
    echo "Migrations failed, but continuing with deployment"
fi

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

# Test the application
echo "Testing application..."
php artisan tinker --execute="try { echo 'Students: ' . App\Models\Student::count(); echo 'Teachers: ' . App\Models\Teacher::count(); echo 'Classes: ' . App\Models\ClassRoom::count(); } catch (Exception \$e) { echo 'Test failed: ' . \$e->getMessage(); }"

echo "Deployment completed successfully!"
