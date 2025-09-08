@echo off
echo ========================================
echo Switching to SQLite Database
echo ========================================
echo.

echo Updating .env file for SQLite...
powershell -Command "(Get-Content .env) -replace 'DB_CONNECTION=mysql', 'DB_CONNECTION=sqlite' -replace 'DB_HOST=127.0.0.1', '' -replace 'DB_PORT=3306', '' -replace 'DB_DATABASE=school_management', 'DB_DATABASE=database/database.sqlite' -replace 'DB_USERNAME=root', '' -replace 'DB_PASSWORD=', '' | Set-Content .env"

echo Clearing Laravel caches...
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
echo.

echo Running migrations...
php artisan migrate --force
echo.

echo Seeding database...
php artisan db:seed --force
echo.

echo ========================================
echo SQLite Setup Complete!
echo ========================================
echo.
echo Your application is now using SQLite database.
echo Access your application at: http://127.0.0.1:8000
echo.
echo To switch back to MySQL later:
echo 1. Install MySQL/XAMPP
echo 2. Run setup_production.bat
echo.
pause
