# MySQL Installation Guide for Windows

## Option 1: XAMPP (Recommended - Easiest)

### Download and Install XAMPP
1. Go to: https://www.apachefriends.org/download.html
2. Download XAMPP for Windows
3. Run the installer as Administrator
4. Select MySQL, Apache, and phpMyAdmin during installation
5. Install to default location (usually C:\xampp)

### Start Services
1. Open XAMPP Control Panel
2. Start **Apache** service
3. Start **MySQL** service
4. Click **Admin** next to MySQL to open phpMyAdmin

### Access phpMyAdmin
- URL: http://localhost/phpmyadmin
- Username: root
- Password: (leave empty)

## Option 2: MySQL Server Only

### Download MySQL
1. Go to: https://dev.mysql.com/downloads/mysql/
2. Download MySQL Installer for Windows
3. Run installer and select "Developer Default"
4. Set root password during installation
5. Start MySQL service

## Database Setup Steps

### Step 1: Create Database
1. Open phpMyAdmin (http://localhost/phpmyadmin)
2. Click "New" in the left sidebar
3. Database name: `school_management`
4. Collation: `utf8mb4_unicode_ci`
5. Click "Create"

### Step 2: Update .env File
Edit your `.env` file with these settings:

**For XAMPP (no password):**
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=school_management
DB_USERNAME=root
DB_PASSWORD=
```

**For MySQL Server (with password):**
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=school_management
DB_USERNAME=root
DB_PASSWORD=your_mysql_password
```

### Step 3: Run Laravel Setup
After MySQL is running, execute:
```bash
php artisan migrate
php artisan db:seed
```

## Quick Test
To test if MySQL is working:
1. Open Command Prompt
2. Run: `mysql -u root -p` (enter password if set)
3. Or access phpMyAdmin at http://localhost/phpmyadmin

## Troubleshooting

### MySQL Service Not Starting
1. Check if port 3306 is in use
2. Run XAMPP as Administrator
3. Check Windows Firewall settings

### Access Denied Error
1. Make sure MySQL service is running
2. Check username/password in .env file
3. Try connecting with phpMyAdmin first

### Port Already in Use
1. Stop other MySQL services
2. Change port in XAMPP settings
3. Update .env file with new port
