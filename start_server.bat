@echo off
echo Starting Laravel Development Server...
echo.

REM Use the PHP path found by Composer
set PHP_PATH="C:\Users\DELL\AppData\Local\Microsoft\WinGet\Packages\PHP.PHP.8.3_Microsoft.Winget.Source_8wekyb3d8bbwe\php.exe"

echo Found PHP at: %PHP_PATH%
echo.
echo Starting server at http://127.0.0.1:8000
echo Press Ctrl+C to stop the server
echo.

REM Start the Laravel server
%PHP_PATH% artisan serve
