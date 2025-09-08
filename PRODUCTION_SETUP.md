# Liberia School Management System - Production Setup Guide

## Prerequisites

### 1. Install MySQL Server
Download and install MySQL Server from: https://dev.mysql.com/downloads/mysql/

**For Windows:**
- Download MySQL Installer
- Install MySQL Server
- Set root password during installation
- Start MySQL service

### 2. Install phpMyAdmin (Optional but Recommended)
Download from: https://www.phpmyadmin.net/downloads/

## Database Setup

### Step 1: Create Database
1. Open MySQL Command Line or phpMyAdmin
2. Run the following commands:

```sql
CREATE DATABASE school_management CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Step 2: Update Database Credentials
Edit the `.env` file and update these values:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=school_management
DB_USERNAME=root
DB_PASSWORD=your_mysql_root_password
```

### Step 3: Run Laravel Migrations
```bash
php artisan migrate
```

### Step 4: Seed Database with Initial Data
```bash
php artisan db:seed
```

## Access Points

### Application URLs
- **Main Application**: http://127.0.0.1:8000
- **Simple Login**: http://127.0.0.1:8000/simple-login
- **Admin Dashboard**: http://127.0.0.1:8000/admin/dashboard

### Database Access
- **phpMyAdmin**: http://localhost/phpmyadmin (if installed)
- **MySQL Command Line**: `mysql -u root -p`

### Default Login Credentials
- **Admin**: admin@school.com / password
- **Teacher**: teacher@school.com / password
- **Student**: student@school.com / password
- **Finance**: finance@school.com / password

## Database Tables Created

The system will create the following main tables:
- `users` - User accounts and authentication
- `students` - Student information
- `teachers` - Teacher information
- `staff` - Staff information
- `class_rooms` - Class information
- `subjects` - Subject information
- `exam_schedules` - Exam scheduling
- `fee_structures` - Fee structure definitions
- `fee_payments` - Fee payment records
- `student_attendances` - Attendance records
- `homeworks` - Homework assignments
- `study_materials` - Study materials
- `scholarships` - Scholarship information
- `notifications` - System notifications
- `settings` - System settings

## Production Configuration

### Security Settings
1. Set `APP_DEBUG=false` in `.env`
2. Set `APP_ENV=production` in `.env`
3. Use strong database passwords
4. Enable HTTPS in production

### Performance Optimization
1. Enable query caching
2. Use Redis for sessions in production
3. Configure proper file permissions
4. Set up regular database backups

## Troubleshooting

### Common Issues
1. **Database Connection Error**: Check MySQL service is running
2. **Migration Errors**: Ensure database exists and user has permissions
3. **CSRF Errors**: Clear browser cache and cookies
4. **Session Issues**: Check session driver configuration

### Support
For technical support, check the Laravel logs in `storage/logs/laravel.log`
