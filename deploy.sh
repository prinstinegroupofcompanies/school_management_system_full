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

# Run force migration to ensure all tables are created
echo "Running force migration..."
php force_migrate.php

# Verify key tables exist
echo "Verifying key tables..."
php -r "
require_once 'vendor/autoload.php';
\$app = require_once 'bootstrap/app.php';
\$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
\$tables = ['users', 'students', 'teachers', 'class_rooms', 'subjects'];
foreach (\$tables as \$table) {
    if (Schema::hasTable(\$table)) {
        echo \"✅ \$table exists\n\";
    } else {
        echo \"❌ \$table missing\n\";
    }
}
"

# Run database seeders for production
echo "Running database seeders..."
php artisan db:seed --force

# Cache configurations for production
echo "Caching configurations..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Deployment completed successfully!"

echo ""
echo "=== Running dashboard data debug ==="
php debug_dashboard.php

echo ""
echo "✅ Deployment and verification complete!"