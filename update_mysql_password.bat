@echo off
echo ========================================
echo Update MySQL Password in .env file
echo ========================================
echo.

echo Please enter your MySQL root password:
set /p mysql_password="MySQL Password: "

echo.
echo Updating .env file with MySQL password...

powershell -Command "(Get-Content .env) -replace 'DB_PASSWORD=', 'DB_PASSWORD=%mysql_password%' | Set-Content .env"

echo.
echo Testing database connection...
php artisan migrate:status

echo.
echo If connection is successful, run the full setup:
echo .\setup_production.bat
echo.
pause
