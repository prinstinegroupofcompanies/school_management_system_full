# 🚀 Liberia School Management System - InfinityFree Deployment Guide

## 📋 Table of Contents
1. [Prerequisites](#prerequisites)
2. [InfinityFree Account Setup](#infinityfree-account-setup)
3. [Project Preparation](#project-preparation)
4. [Database Setup](#database-setup)
5. [File Upload Process](#file-upload-process)
6. [Configuration](#configuration)
7. [Testing & Verification](#testing--verification)
8. [Troubleshooting](#troubleshooting)
9. [Maintenance & Updates](#maintenance--updates)

---

## 🎯 Prerequisites

### System Requirements
- **PHP Version**: 8.2+ (InfinityFree supports PHP 8.2)
- **Database**: MySQL 5.7+ (InfinityFree provides MySQL 5.7)
- **Web Server**: Apache (InfinityFree uses Apache)
- **Storage**: Minimum 1GB (InfinityFree free tier provides 5GB)

### Local Development Setup
- Laravel 11.x project
- Composer installed
- Git for version control
- Code editor (VS Code, PhpStorm, etc.)

---

## 🔧 InfinityFree Account Setup

### Step 1: Create InfinityFree Account
1. Visit [https://infinityfree.net](https://infinityfree.net)
2. Click "Sign Up" and create a free account
3. Verify your email address
4. Log in to your account

### Step 2: Create a New Hosting Account
1. Go to "Client Area" → "Create New Account"
2. Choose a subdomain (e.g., `yourschool.infinityfreeapp.com`)
3. Select PHP 8.2
4. Choose a data center location (closest to your users)
5. Complete the account creation

### Step 3: Access Control Panel
1. Log in to your hosting control panel
2. Note down your FTP credentials
3. Access phpMyAdmin for database management

---

## 📦 Project Preparation

### Step 1: Optimize for Production

Create a production-ready version of your project:

```bash
# 1. Install dependencies (production only)
composer install --optimize-autoloader --no-dev

# 2. Generate application key
php artisan key:generate

# 3. Clear and cache configurations
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 4. Optimize autoloader
composer dump-autoload --optimize
```

### Step 2: Create .htaccess for InfinityFree

Create `public/.htaccess` with InfinityFree-specific configuration:

```apache
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
```

### Step 3: Create Production Environment File

Create `production.env` with InfinityFree settings:

```env
APP_NAME="Liberia School Management System"
APP_ENV=production
APP_KEY=base64:YOUR_GENERATED_KEY_HERE
APP_DEBUG=false
APP_URL=https://yourschool.infinityfreeapp.com

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=sqlXXX.infinityfree.com
DB_PORT=3306
DB_DATABASE=if0_XXXXXXX_school_management
DB_USERNAME=if0_XXXXXXX
DB_PASSWORD=your_database_password

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@yourschool.com"
MAIL_FROM_NAME="${APP_NAME}"

# Currency and Locale
CURRENCY=LRD
LOCALE=en
TIMEZONE=Africa/Monrovia

# File Upload Settings
MAX_FILE_SIZE=10240
ALLOWED_FILE_TYPES=jpg,jpeg,png,pdf,doc,docx,mp4,mp3

# Backup Settings
BACKUP_ENABLED=true
BACKUP_RETENTION_DAYS=30
```

---

## 🗄️ Database Setup

### Step 1: Create Database in phpMyAdmin
1. Log in to your InfinityFree control panel
2. Go to "Databases" → "phpMyAdmin"
3. Click "New" to create a new database
4. Name it: `if0_XXXXXXX_school_management` (replace XXXXXXX with your account ID)
5. Set collation to `utf8mb4_unicode_ci`

### Step 2: Import Database Structure
1. In phpMyAdmin, select your database
2. Go to "Import" tab
3. Upload the `database_structure.sql` file (created in next step)
4. Click "Go" to import

### Step 3: Create Database Structure File

Create `database_structure.sql` with your database schema:

```sql
-- Create database structure for InfinityFree
-- This file should contain all your migration SQL

-- Example tables (replace with your actual schema)
CREATE TABLE IF NOT EXISTS `users` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `email` varchar(255) NOT NULL,
    `email_verified_at` timestamp NULL DEFAULT NULL,
    `password` varchar(255) NOT NULL,
    `role` varchar(50) DEFAULT 'student',
    `remember_token` varchar(100) DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add all other tables from your migrations
-- (Copy the SQL from your migration files)
```

---

## 📁 File Upload Process

### Step 1: Prepare Files for Upload

Create a deployment package with only necessary files:

```
school-management-system/
├── app/
├── bootstrap/
├── config/
├── database/
├── public/
│   ├── .htaccess
│   ├── index.php
│   └── storage -> ../storage/app/public
├── resources/
├── routes/
├── storage/
│   ├── app/
│   ├── framework/
│   └── logs/
├── vendor/
├── .env (production version)
├── artisan
└── composer.json
```

### Step 2: Upload via File Manager
1. Log in to InfinityFree control panel
2. Go to "File Manager"
3. Navigate to `htdocs` folder
4. Upload all files maintaining the directory structure
5. Set proper permissions (755 for folders, 644 for files)

### Step 3: Set File Permissions
```bash
# Set permissions for storage and cache directories
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
```

---

## ⚙️ Configuration

### Step 1: Environment Configuration
1. Rename `production.env` to `.env` on the server
2. Update database credentials with your InfinityFree details
3. Set `APP_URL` to your InfinityFree domain
4. Generate a new application key: `php artisan key:generate`

### Step 2: Database Migration
1. Access your site via SSH or use the online terminal
2. Run migrations: `php artisan migrate --force`
3. Seed initial data: `php artisan db:seed --force`

### Step 3: Storage Link
```bash
php artisan storage:link
```

### Step 4: Clear Caches
```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```

---

## 🧪 Testing & Verification

### Step 1: Basic Functionality Test
1. Visit your site: `https://yourschool.infinityfreeapp.com`
2. Check if the homepage loads correctly
3. Test login functionality
4. Verify all dashboard pages work

### Step 2: Database Connection Test
1. Try creating a new user
2. Check if data is saved to database
3. Verify all CRUD operations work

### Step 3: File Upload Test
1. Test image uploads
2. Check if files are stored correctly
3. Verify file permissions

### Step 4: Performance Test
1. Check page load times
2. Test with multiple users
3. Monitor memory usage

---

## 🔧 Troubleshooting

### Common Issues and Solutions

#### 1. 500 Internal Server Error
**Causes:**
- Incorrect file permissions
- Missing .env file
- Database connection issues
- PHP version mismatch

**Solutions:**
```bash
# Check file permissions
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/

# Verify .env file exists and has correct settings
# Check database credentials

# Clear caches
php artisan config:clear
php artisan cache:clear
```

#### 2. Database Connection Error
**Causes:**
- Wrong database credentials
- Database not created
- MySQL service not running

**Solutions:**
- Verify database credentials in .env
- Check database exists in phpMyAdmin
- Ensure database user has proper permissions

#### 3. File Upload Issues
**Causes:**
- Incorrect file permissions
- PHP upload limits
- Storage directory not writable

**Solutions:**
```bash
# Set correct permissions
chmod -R 775 storage/
chmod -R 775 public/

# Check PHP upload limits in .htaccess
php_value upload_max_filesize 10M
php_value post_max_size 10M
```

#### 4. CSS/JS Not Loading
**Causes:**
- Incorrect .htaccess configuration
- Missing storage link
- File path issues

**Solutions:**
```bash
# Create storage link
php artisan storage:link

# Check .htaccess rewrite rules
# Verify file paths in views
```

#### 5. Session Issues
**Causes:**
- Incorrect session configuration
- File permission issues
- Session directory not writable

**Solutions:**
```bash
# Set correct permissions for session directory
chmod -R 775 storage/framework/sessions/

# Check session configuration in .env
SESSION_DRIVER=file
```

### Debug Mode
For debugging, temporarily enable debug mode:
```env
APP_DEBUG=true
LOG_LEVEL=debug
```

**Remember to disable debug mode in production!**

---

## 🔄 Maintenance & Updates

### Regular Maintenance Tasks

#### 1. Database Backups
```bash
# Create database backup
mysqldump -u username -p database_name > backup_$(date +%Y%m%d).sql

# Restore from backup
mysql -u username -p database_name < backup_file.sql
```

#### 2. Log Monitoring
```bash
# Check error logs
tail -f storage/logs/laravel.log

# Clear old logs
php artisan log:clear
```

#### 3. Cache Management
```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Rebuild caches
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

#### 4. Security Updates
- Keep Laravel and dependencies updated
- Monitor security advisories
- Regular security scans

### Performance Optimization

#### 1. Enable Caching
```env
CACHE_DRIVER=file
SESSION_DRIVER=file
```

#### 2. Optimize Database
- Regular database maintenance
- Index optimization
- Query optimization

#### 3. File Optimization
- Compress images
- Minify CSS/JS
- Use CDN for static assets

---

## 📞 Support Resources

### InfinityFree Support
- **Documentation**: [https://infinityfree.net/support/](https://infinityfree.net/support/)
- **Community Forum**: [https://forum.infinityfree.net/](https://forum.infinityfree.net/)
- **Knowledge Base**: [https://infinityfree.net/kb/](https://infinityfree.net/kb/)

### Laravel Resources
- **Documentation**: [https://laravel.com/docs](https://laravel.com/docs)
- **Community**: [https://laracasts.com/](https://laracasts.com/)
- **Stack Overflow**: [https://stackoverflow.com/questions/tagged/laravel](https://stackoverflow.com/questions/tagged/laravel)

---

## ✅ Deployment Checklist

- [ ] InfinityFree account created
- [ ] Database created in phpMyAdmin
- [ ] Project files uploaded to htdocs
- [ ] .env file configured with production settings
- [ ] File permissions set correctly
- [ ] Database migrations run successfully
- [ ] Storage link created
- [ ] Caches cleared and rebuilt
- [ ] Application key generated
- [ ] Site accessible via domain
- [ ] Login functionality working
- [ ] Database operations working
- [ ] File uploads working
- [ ] Debug mode disabled
- [ ] Security headers configured
- [ ] Backup strategy implemented

---

## 🎉 Success!

Your Liberia School Management System should now be successfully deployed on InfinityFree! 

**Your site URL**: `https://yourschool.infinityfreeapp.com`

**Default Login Credentials:**
- **Admin**: admin@school.com / password
- **Teacher**: teacher@school.com / password
- **Student**: student@school.com / password
- **Finance**: finance@school.com / password

Remember to change these default passwords after your first login!

---

*This guide was created specifically for the Liberia School Management System deployment on InfinityFree. For additional support or custom configurations, please refer to the official documentation or contact the development team.*
