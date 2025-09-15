#!/bin/bash

# ========================================
# Liberia School Management System
# InfinityFree Setup Script
# ========================================

echo "🚀 Setting up Liberia School Management System for InfinityFree deployment..."

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    echo "❌ ERROR: This script must be run from the Laravel project root directory"
    echo "Please navigate to the project folder and run this script again"
    exit 1
fi

echo "📦 Step 1: Installing production dependencies..."
composer install --optimize-autoloader --no-dev --no-interaction
if [ $? -ne 0 ]; then
    echo "❌ ERROR: Failed to install dependencies"
    exit 1
fi

echo "🔑 Step 2: Generating application key..."
php artisan key:generate --force
if [ $? -ne 0 ]; then
    echo "❌ ERROR: Failed to generate application key"
    exit 1
fi

echo "🧹 Step 3: Clearing caches..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

echo "⚡ Step 4: Optimizing for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "📁 Step 5: Creating deployment package..."
if [ -d "deployment_package" ]; then
    rm -rf "deployment_package"
fi
mkdir "deployment_package"

# Copy necessary files and directories
echo "📋 Copying application files..."
cp -r app deployment_package/
cp -r bootstrap deployment_package/
cp -r config deployment_package/
cp -r database deployment_package/
cp -r public deployment_package/
cp -r resources deployment_package/
cp -r routes deployment_package/
cp -r storage deployment_package/
cp -r vendor deployment_package/

# Copy individual files
cp artisan deployment_package/
cp composer.json deployment_package/
cp composer.lock deployment_package/

echo "⚙️ Step 6: Creating production .env file..."
if [ -f "production.env" ]; then
    cp production.env deployment_package/.env
    echo "✅ Production .env file copied"
else
    echo "⚠️  WARNING: production.env file not found"
    echo "Please create production.env with your InfinityFree settings"
fi

echo "🌐 Step 7: Creating .htaccess for InfinityFree..."
cat > deployment_package/public/.htaccess << 'EOF'
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Angular and Vue.js routes
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ index.php [QSA,L]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>

# Security Headers
<IfModule mod_headers.c>
    Header always set X-Content-Type-Options nosniff
    Header always set X-Frame-Options DENY
    Header always set X-XSS-Protection "1; mode=block"
</IfModule>

# Disable directory browsing
Options -Indexes

# Protect sensitive files
<Files ".env">
    Order allow,deny
    Deny from all
</Files>

<Files "composer.json">
    Order allow,deny
    Deny from all
</Files>

<Files "composer.lock">
    Order allow,deny
    Deny from all
</Files>

# PHP Settings
php_value upload_max_filesize 10M
php_value post_max_size 10M
php_value max_execution_time 300
php_value memory_limit 256M
EOF

echo "🗄️ Step 8: Creating database structure file..."
php artisan schema:dump --database=mysql > deployment_package/database_structure.sql
if [ $? -ne 0 ]; then
    echo "⚠️  WARNING: Failed to create database structure file"
    echo "You may need to create this manually from your migrations"
fi

echo "📋 Step 9: Creating deployment instructions..."
cat > deployment_package/DEPLOYMENT_INSTRUCTIONS.txt << 'EOF'
========================================
Liberia School Management System
InfinityFree Deployment Instructions
========================================

1. UPLOAD FILES
   - Upload all files from this package to your InfinityFree htdocs folder
   - Maintain the directory structure

2. CREATE DATABASE
   - Go to phpMyAdmin in your InfinityFree control panel
   - Create a new database: if0_XXXXXXX_school_management
   - Import the database_structure.sql file

3. CONFIGURE ENVIRONMENT
   - Update the .env file with your InfinityFree database credentials
   - Set APP_URL to your InfinityFree domain
   - Generate a new application key: php artisan key:generate

4. SET PERMISSIONS
   - Set 755 permissions for storage/ and bootstrap/cache/ directories
   - Set 644 permissions for .env and other files

5. RUN MIGRATIONS
   - Run: php artisan migrate --force
   - Run: php artisan storage:link

6. TEST YOUR SITE
   - Visit your domain to test the application
   - Check all functionality works correctly

For detailed instructions, see INFINITYFREE_DEPLOYMENT_GUIDE.md
For troubleshooting, see infinityfree_troubleshooting.md
EOF

echo "🔧 Step 10: Setting up file permissions..."
chmod -R 755 deployment_package/storage/
chmod -R 755 deployment_package/bootstrap/cache/
chmod 644 deployment_package/.env
chmod 644 deployment_package/composer.json

echo ""
echo "========================================"
echo "✅ DEPLOYMENT PACKAGE READY!"
echo "========================================"
echo ""
echo "📁 The deployment package has been created in the 'deployment_package' folder"
echo ""
echo "📋 Next steps:"
echo "1. Upload the contents of 'deployment_package' to your InfinityFree htdocs folder"
echo "2. Create your database in phpMyAdmin"
echo "3. Import the database_structure.sql file"
echo "4. Update the .env file with your InfinityFree database credentials"
echo "5. Set proper file permissions on the server"
echo "6. Run migrations: php artisan migrate --force"
echo "7. Create storage link: php artisan storage:link"
echo ""
echo "📖 For detailed instructions, see INFINITYFREE_DEPLOYMENT_GUIDE.md"
echo "🔧 For troubleshooting, see infinityfree_troubleshooting.md"
echo ""
echo "🎉 Happy deploying!"
