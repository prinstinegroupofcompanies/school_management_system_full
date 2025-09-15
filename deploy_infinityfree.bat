@echo off
echo ========================================
echo Liberia School Management System
echo InfinityFree Deployment Script
echo ========================================
echo.

REM Check if we're in the right directory
if not exist "artisan" (
    echo ERROR: This script must be run from the Laravel project root directory
    echo Please navigate to the project folder and run this script again
    pause
    exit /b 1
)

echo Step 1: Installing production dependencies...
call composer install --optimize-autoloader --no-dev --no-interaction
if errorlevel 1 (
    echo ERROR: Failed to install dependencies
    pause
    exit /b 1
)

echo.
echo Step 2: Generating application key...
php artisan key:generate --force
if errorlevel 1 (
    echo ERROR: Failed to generate application key
    pause
    exit /b 1
)

echo.
echo Step 3: Clearing caches...
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

echo.
echo Step 4: Optimizing for production...
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo.
echo Step 5: Creating deployment package...
if exist "deployment_package" rmdir /s /q "deployment_package"
mkdir "deployment_package"

REM Copy necessary files and directories
echo Copying application files...
xcopy "app" "deployment_package\app" /E /I /Q
xcopy "bootstrap" "deployment_package\bootstrap" /E /I /Q
xcopy "config" "deployment_package\config" /E /I /Q
xcopy "database" "deployment_package\database" /E /I /Q
xcopy "public" "deployment_package\public" /E /I /Q
xcopy "resources" "deployment_package\resources" /E /I /Q
xcopy "routes" "deployment_package\routes" /E /I /Q
xcopy "storage" "deployment_package\storage" /E /I /Q
xcopy "vendor" "deployment_package\vendor" /E /I /Q

REM Copy individual files
copy "artisan" "deployment_package\artisan"
copy "composer.json" "deployment_package\composer.json"
copy "composer.lock" "deployment_package\composer.lock"

echo.
echo Step 6: Creating production .env file...
if exist "production.env" (
    copy "production.env" "deployment_package\.env"
    echo Production .env file copied
) else (
    echo WARNING: production.env file not found
    echo Please create production.env with your InfinityFree settings
)

echo.
echo Step 7: Creating .htaccess for InfinityFree...
echo ^<IfModule mod_rewrite.c^> > "deployment_package\public\.htaccess"
echo     ^<IfModule mod_negotiation.c^> >> "deployment_package\public\.htaccess"
echo         Options -MultiViews -Indexes >> "deployment_package\public\.htaccess"
echo     ^</IfModule^> >> "deployment_package\public\.htaccess"
echo. >> "deployment_package\public\.htaccess"
echo     RewriteEngine On >> "deployment_package\public\.htaccess"
echo. >> "deployment_package\public\.htaccess"
echo     # Handle Angular and Vue.js routes >> "deployment_package\public\.htaccess"
echo     RewriteCond %%{REQUEST_FILENAME} !-f >> "deployment_package\public\.htaccess"
echo     RewriteCond %%{REQUEST_FILENAME} !-d >> "deployment_package\public\.htaccess"
echo     RewriteRule ^^(.*^)$ index.php [QSA,L] >> "deployment_package\public\.htaccess"
echo. >> "deployment_package\public\.htaccess"
echo     # Redirect Trailing Slashes If Not A Folder... >> "deployment_package\public\.htaccess"
echo     RewriteCond %%{REQUEST_FILENAME} !-d >> "deployment_package\public\.htaccess"
echo     RewriteCond %%{REQUEST_URI} ^(.+^)/$ >> "deployment_package\public\.htaccess"
echo     RewriteRule ^ %%1 [L,R=301] >> "deployment_package\public\.htaccess"
echo. >> "deployment_package\public\.htaccess"
echo     # Send Requests To Front Controller... >> "deployment_package\public\.htaccess"
echo     RewriteCond %%{REQUEST_FILENAME} !-d >> "deployment_package\public\.htaccess"
echo     RewriteCond %%{REQUEST_FILENAME} !-f >> "deployment_package\public\.htaccess"
echo     RewriteRule ^ index.php [L] >> "deployment_package\public\.htaccess"
echo ^</IfModule^> >> "deployment_package\public\.htaccess"
echo. >> "deployment_package\public\.htaccess"
echo # Security Headers >> "deployment_package\public\.htaccess"
echo ^<IfModule mod_headers.c^> >> "deployment_package\public\.htaccess"
echo     Header always set X-Content-Type-Options nosniff >> "deployment_package\public\.htaccess"
echo     Header always set X-Frame-Options DENY >> "deployment_package\public\.htaccess"
echo     Header always set X-XSS-Protection "1; mode=block" >> "deployment_package\public\.htaccess"
echo ^</IfModule^> >> "deployment_package\public\.htaccess"
echo. >> "deployment_package\public\.htaccess"
echo # Disable directory browsing >> "deployment_package\public\.htaccess"
echo Options -Indexes >> "deployment_package\public\.htaccess"
echo. >> "deployment_package\public\.htaccess"
echo # Protect sensitive files >> "deployment_package\public\.htaccess"
echo ^<Files ".env"^> >> "deployment_package\public\.htaccess"
echo     Order allow,deny >> "deployment_package\public\.htaccess"
echo     Deny from all >> "deployment_package\public\.htaccess"
echo ^</Files^> >> "deployment_package\public\.htaccess"

echo.
echo Step 8: Creating database structure file...
php artisan schema:dump --database=mysql > "deployment_package\database_structure.sql"
if errorlevel 1 (
    echo WARNING: Failed to create database structure file
    echo You may need to create this manually from your migrations
)

echo.
echo ========================================
echo DEPLOYMENT PACKAGE READY!
echo ========================================
echo.
echo The deployment package has been created in the 'deployment_package' folder
echo.
echo Next steps:
echo 1. Upload the contents of 'deployment_package' to your InfinityFree htdocs folder
echo 2. Create your database in phpMyAdmin
echo 3. Import the database_structure.sql file
echo 4. Update the .env file with your InfinityFree database credentials
echo 5. Set proper file permissions on the server
echo 6. Run migrations: php artisan migrate --force
echo 7. Create storage link: php artisan storage:link
echo.
echo For detailed instructions, see INFINITYFREE_DEPLOYMENT_GUIDE.md
echo.
pause
