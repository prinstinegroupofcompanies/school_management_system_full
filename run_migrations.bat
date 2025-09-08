@echo off
echo Starting Laravel Migrations...
echo.

REM Use the PHP path found by Composer
set PHP_PATH=C:\Users\DELL\AppData\Local\Microsoft\WinGet\Packages\PHP.PHP.8.3_Microsoft.Winget.Source_8wekyb3d8bbwe\php.exe

echo Found PHP at: %PHP_PATH%
echo.

REM Install Composer dependencies
echo Installing Composer dependencies...
call composer install
if errorlevel 1 (
    echo Composer install failed!
    pause
    exit /b 1
)
echo.

REM Run migrations
echo Running migrations...
<?php
public function handle($request, Closure $next, ...$roles)
{
    if (!auth()->check() || !in_array(auth()->user()->role, $roles)) {
        abort(403, 'Unauthorized');
    }
    return $next($request);
}
if errorlevel 1 (
    echo Migrations failed!
    pause
    exit /b 1
)
echo.
echo Migrations completed!

REM Start Laravel server
echo Starting Laravel server at <?php
// Run in terminal:
// filepath: database/migrations/xxxx_xx_xx_xxxxxx_add_role_to_users_table.php
php artisan make:migration add_role_to_users_table --<?php
public function up()
{
    Schema::table('users', function (Blueprint $table) {
        $table->string('role')->default('student'); // or nullable if you want
    });
}

public function down()
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn('role');
    });
}table=users ...
start "" %PHP_PATH% artisan serve

pause
