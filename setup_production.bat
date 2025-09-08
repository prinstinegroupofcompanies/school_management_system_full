@echo off
echo ========================================
echo Liberia School Management System
echo Production Setup Script
echo ========================================
echo.

echo Step 1: Clearing Laravel caches...
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
echo.

echo Step 2: Testing database connection...
php artisan migrate:status
echo.

echo Step 3: Running database migrations...
php artisan migrate --force
echo.

echo Step 4: Seeding database with initial data...
php artisan db:seed --force
echo.

echo Step 5: Creating storage links...
php artisan storage:link
echo.

echo Step 6: Optimizing for production...
php artisan config:cache
php artisan route:cache
php artisan view:cache
echo.

echo ========================================
echo Setup Complete!
echo ========================================
echo.
echo Access your application at:
echo - Main App: http://127.0.0.1:8000
echo - Simple Login: http://127.0.0.1:8000/simple-login
echo - Admin Dashboard: http://127.0.0.1:8000/admin/dashboard
echo.
echo Default Login Credentials:
echo - Admin: admin@school.com / password
echo - Teacher: teacher@school.com / password
echo - Student: student@school.com / password
echo - Finance: finance@school.com / password
echo.
echo Database Access:
echo - phpMyAdmin: http://localhost/phpmyadmin
echo - Database: school_management
echo.
pause
