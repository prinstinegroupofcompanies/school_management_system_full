# ⚡ Quick Start Guide - InfinityFree Deployment

## 🚀 Get Your School Management System Online in 15 Minutes!

### Prerequisites
- [ ] InfinityFree account (free at [infinityfree.net](https://infinityfree.net))
- [ ] Your Laravel project ready
- [ ] Basic understanding of file uploads

---

## 📋 Step-by-Step Deployment

### 1. Prepare Your Project (5 minutes)

**Option A: Use the automated script (Windows)**
```cmd
deploy_infinityfree.bat
```

**Option B: Use the automated script (Linux/Mac)**
```bash
chmod +x setup_infinityfree.sh
./setup_infinityfree.sh
```

**Option C: Manual preparation**
```bash
# Install production dependencies
composer install --optimize-autoloader --no-dev

# Generate application key
php artisan key:generate

# Clear and cache configurations
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 2. Create InfinityFree Account (2 minutes)

1. Go to [https://infinityfree.net](https://infinityfree.net)
2. Click "Sign Up" and create account
3. Verify your email
4. Create new hosting account with PHP 8.2
5. Note your FTP credentials and database details

### 3. Upload Files (3 minutes)

1. Go to InfinityFree control panel → File Manager
2. Navigate to `htdocs` folder
3. Upload all files from `deployment_package` folder
4. Maintain directory structure

### 4. Setup Database (3 minutes)

1. Go to phpMyAdmin in control panel
2. Create database: `if0_XXXXXXX_school_management`
3. Import `database_structure.sql` file
4. Note your database credentials

### 5. Configure Environment (2 minutes)

1. Edit `.env` file on server
2. Update database credentials:
   ```env
   DB_HOST=sqlXXX.infinityfree.com
   DB_DATABASE=if0_XXXXXXX_school_management
   DB_USERNAME=if0_XXXXXXX
   DB_PASSWORD=your_password
   APP_URL=https://yourschool.infinityfreeapp.com
   ```

### 6. Final Setup (2 minutes)

1. Set file permissions (755 for folders, 644 for files)
2. Run migrations: `php artisan migrate --force`
3. Create storage link: `php artisan storage:link`
4. Test your site!

---

## 🎯 Your Site is Live!

**URL**: `https://yourschool.infinityfreeapp.com`

**Default Login Credentials:**
- **Admin**: admin@school.com / password
- **Teacher**: teacher@school.com / password
- **Student**: student@school.com / password
- **Finance**: finance@school.com / password

---

## 🔧 Quick Troubleshooting

### Site Not Loading?
1. Check `.env` file exists and has correct settings
2. Verify database credentials
3. Check file permissions (755 for storage/, 644 for .env)

### Database Error?
1. Verify database exists in phpMyAdmin
2. Check database credentials in `.env`
3. Run migrations: `php artisan migrate --force`

### Files Not Uploading?
1. Check storage permissions: `chmod 775 storage/`
2. Create storage link: `php artisan storage:link`
3. Verify `.htaccess` file is in public folder

---

## 📚 Need More Help?

- **Full Guide**: `INFINITYFREE_DEPLOYMENT_GUIDE.md`
- **Troubleshooting**: `infinityfree_troubleshooting.md`
- **InfinityFree Support**: [https://infinityfree.net/support/](https://infinityfree.net/support/)

---

## ✅ Deployment Checklist

- [ ] Project prepared with deployment script
- [ ] InfinityFree account created
- [ ] Files uploaded to htdocs
- [ ] Database created and imported
- [ ] .env file configured
- [ ] File permissions set
- [ ] Migrations run
- [ ] Storage link created
- [ ] Site tested and working

---

**🎉 Congratulations! Your School Management System is now live on InfinityFree!**
