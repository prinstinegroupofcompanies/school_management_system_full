# 🔧 InfinityFree Deployment Troubleshooting Guide

## 🚨 Common Issues and Solutions

### 1. 500 Internal Server Error

#### Symptoms:
- Blank white page
- "500 Internal Server Error" message
- Site not loading at all

#### Causes & Solutions:

**A. Missing .env file**
```bash
# Solution: Create .env file with correct settings
# Copy from production.env and update with your InfinityFree credentials
```

**B. Incorrect file permissions**
```bash
# Solution: Set correct permissions
chmod 755 storage/
chmod 755 bootstrap/cache/
chmod 644 .env
chmod 644 composer.json
```

**C. Database connection issues**
```env
# Check your .env file has correct database credentials:
DB_CONNECTION=mysql
DB_HOST=sqlXXX.infinityfree.com
DB_PORT=3306
DB_DATABASE=if0_XXXXXXX_school_management
DB_USERNAME=if0_XXXXXXX
DB_PASSWORD=your_database_password
```

**D. Missing application key**
```bash
# Generate application key
php artisan key:generate
```

**E. PHP version mismatch**
- Ensure your InfinityFree account is set to PHP 8.2
- Check in control panel → PHP Settings

### 2. Database Connection Error

#### Symptoms:
- "SQLSTATE[HY000] [2002] Connection refused"
- "SQLSTATE[HY000] [1045] Access denied"
- Database-related errors

#### Solutions:

**A. Verify database credentials**
```env
# Double-check these in your .env file:
DB_HOST=sqlXXX.infinityfree.com  # Get from control panel
DB_DATABASE=if0_XXXXXXX_school_management  # Your actual database name
DB_USERNAME=if0_XXXXXXX  # Your actual username
DB_PASSWORD=your_actual_password  # Your actual password
```

**B. Check database exists**
1. Go to phpMyAdmin
2. Verify database exists
3. Check if tables are created

**C. Test database connection**
```php
// Create test_db.php in public folder
<?php
$host = 'sqlXXX.infinityfree.com';
$dbname = 'if0_XXXXXXX_school_management';
$username = 'if0_XXXXXXX';
$password = 'your_password';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    echo "Database connection successful!";
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
```

### 3. File Upload Issues

#### Symptoms:
- "File too large" errors
- Uploads not working
- Files not saving

#### Solutions:

**A. Check PHP upload limits**
Create `.htaccess` in public folder:
```apache
php_value upload_max_filesize 10M
php_value post_max_size 10M
php_value max_execution_time 300
php_value max_input_time 300
```

**B. Set correct permissions**
```bash
chmod 775 storage/app/public/
chmod 775 storage/framework/
chmod 775 storage/logs/
```

**C. Create storage link**
```bash
php artisan storage:link
```

### 4. CSS/JS Not Loading

#### Symptoms:
- Styling not applied
- JavaScript not working
- Broken layout

#### Solutions:

**A. Check .htaccess configuration**
```apache
# Ensure this is in public/.htaccess
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]
```

**B. Verify storage link**
```bash
php artisan storage:link
```

**C. Clear caches**
```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```

### 5. Session Issues

#### Symptoms:
- Users getting logged out frequently
- Session data not persisting
- Login not working

#### Solutions:

**A. Check session configuration**
```env
SESSION_DRIVER=file
SESSION_LIFETIME=120
```

**B. Set correct permissions**
```bash
chmod 775 storage/framework/sessions/
```

**C. Clear session files**
```bash
# Delete old session files
rm storage/framework/sessions/*
```

### 6. Migration Errors

#### Symptoms:
- "Table doesn't exist" errors
- Migration failures
- Database structure issues

#### Solutions:

**A. Run migrations**
```bash
php artisan migrate --force
```

**B. Check database connection first**
```bash
php artisan tinker
# Then test: DB::connection()->getPdo();
```

**C. Import database structure manually**
1. Go to phpMyAdmin
2. Select your database
3. Import `infinityfree_setup.sql`

### 7. Memory Limit Issues

#### Symptoms:
- "Fatal error: Allowed memory size exhausted"
- Script timeouts
- Performance issues

#### Solutions:

**A. Increase memory limit in .htaccess**
```apache
php_value memory_limit 256M
php_value max_execution_time 300
```

**B. Optimize application**
```bash
# Clear caches
php artisan optimize:clear

# Rebuild optimized caches
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 8. Email Configuration Issues

#### Symptoms:
- Emails not sending
- SMTP errors
- Mail configuration problems

#### Solutions:

**A. Configure SMTP settings**
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls
```

**B. Test email configuration**
```php
// Create test_email.php in public folder
<?php
use Illuminate\Support\Facades\Mail;

Mail::raw('Test email', function ($message) {
    $message->to('test@example.com')->subject('Test Email');
});
echo "Email sent successfully!";
?>
```

### 9. SSL/HTTPS Issues

#### Symptoms:
- Mixed content warnings
- SSL certificate errors
- Security warnings

#### Solutions:

**A. Force HTTPS in .htaccess**
```apache
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

**B. Update APP_URL**
```env
APP_URL=https://yourschool.infinityfreeapp.com
```

### 10. Performance Issues

#### Symptoms:
- Slow page loads
- High server load
- Timeout errors

#### Solutions:

**A. Enable caching**
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

**B. Optimize database**
```sql
-- Add indexes for frequently queried columns
CREATE INDEX idx_students_class_room_id ON students(class_room_id);
CREATE INDEX idx_attendances_date ON student_attendances(date);
```

**C. Reduce file size**
- Compress images
- Minify CSS/JS
- Remove unused files

## 🔍 Debugging Steps

### 1. Enable Debug Mode (Temporarily)
```env
APP_DEBUG=true
LOG_LEVEL=debug
```

### 2. Check Error Logs
```bash
# Check Laravel logs
tail -f storage/logs/laravel.log

# Check server error logs in InfinityFree control panel
```

### 3. Test Database Connection
```php
// Create db_test.php in public folder
<?php
require_once 'vendor/autoload.php';

$config = [
    'driver' => 'mysql',
    'host' => 'sqlXXX.infinityfree.com',
    'database' => 'if0_XXXXXXX_school_management',
    'username' => 'if0_XXXXXXX',
    'password' => 'your_password',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
];

try {
    $pdo = new PDO(
        "mysql:host={$config['host']};dbname={$config['database']};charset={$config['charset']}",
        $config['username'],
        $config['password']
    );
    echo "✅ Database connection successful!";
} catch (PDOException $e) {
    echo "❌ Database connection failed: " . $e->getMessage();
}
?>
```

### 4. Test File Permissions
```php
// Create permissions_test.php in public folder
<?php
$directories = [
    'storage',
    'storage/app',
    'storage/framework',
    'storage/framework/cache',
    'storage/framework/sessions',
    'storage/framework/views',
    'storage/logs',
    'bootstrap/cache'
];

foreach ($directories as $dir) {
    if (is_dir($dir)) {
        $perms = substr(sprintf('%o', fileperms($dir)), -4);
        echo "✅ $dir: $perms<br>";
    } else {
        echo "❌ $dir: Directory not found<br>";
    }
}
?>
```

## 📞 Getting Help

### 1. Check InfinityFree Status
- Visit [https://infinityfree.net/status/](https://infinityfree.net/status/)
- Check for any ongoing issues

### 2. InfinityFree Support
- **Documentation**: [https://infinityfree.net/support/](https://infinityfree.net/support/)
- **Community Forum**: [https://forum.infinityfree.net/](https://forum.infinityfree.net/)
- **Knowledge Base**: [https://infinityfree.net/kb/](https://infinityfree.net/kb/)

### 3. Laravel Resources
- **Documentation**: [https://laravel.com/docs](https://laravel.com/docs)
- **Stack Overflow**: [https://stackoverflow.com/questions/tagged/laravel](https://stackoverflow.com/questions/tagged/laravel)

### 4. Common Error Codes

| Error Code | Meaning | Solution |
|------------|---------|----------|
| 500 | Internal Server Error | Check .env file, permissions, database connection |
| 404 | Not Found | Check .htaccess, file paths |
| 403 | Forbidden | Check file permissions |
| 502 | Bad Gateway | Check PHP version, server configuration |
| 503 | Service Unavailable | Check server status, resource limits |

## ✅ Quick Fix Checklist

- [ ] .env file exists and has correct settings
- [ ] Database credentials are correct
- [ ] File permissions are set correctly (755 for folders, 644 for files)
- [ ] Storage link is created (`php artisan storage:link`)
- [ ] Caches are cleared and rebuilt
- [ ] Application key is generated
- [ ] Database tables exist and are populated
- [ ] .htaccess file is in public folder
- [ ] PHP version is 8.2
- [ ] Debug mode is disabled in production

## 🚀 Performance Optimization

### 1. Enable All Caches
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

### 2. Optimize Composer
```bash
composer install --optimize-autoloader --no-dev
```

### 3. Database Optimization
```sql
-- Add indexes for better performance
CREATE INDEX idx_students_class_room_id ON students(class_room_id);
CREATE INDEX idx_attendances_date ON student_attendances(date);
CREATE INDEX idx_fee_payments_student_id ON fee_payments(student_id);
```

### 4. File Optimization
- Compress images before upload
- Use appropriate image formats (WebP, JPEG)
- Minify CSS and JavaScript files
- Remove unused files and dependencies

---

*This troubleshooting guide covers the most common issues when deploying Laravel applications on InfinityFree. For specific issues not covered here, please refer to the official documentation or seek help from the community forums.*
