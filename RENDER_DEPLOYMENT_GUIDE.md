# School Management System - Render Deployment Guide

## Overview
This guide will help you deploy the School Management System to Render, a modern cloud platform that supports PHP applications with automatic deployments from Git repositories.

## Prerequisites
- Render account (free tier available)
- Git repository (GitHub, GitLab, or Bitbucket)
- Database (PostgreSQL recommended for Render)

## Step 1: Prepare Your Repository

### 1.1 Push to Git Repository
```bash
git add .
git commit -m "Prepare for Render deployment"
git push origin main
```

### 1.2 Create Render Configuration Files

Create a `render.yaml` file in your project root:

```yaml
services:
  - type: web
    name: school-management-system
    env: php
    plan: free
    buildCommand: composer install --no-dev --optimize-autoloader
    startCommand: php artisan serve --host=0.0.0.0 --port=$PORT
    envVars:
      - key: APP_ENV
        value: production
      - key: APP_DEBUG
        value: false
      - key: LOG_CHANNEL
        value: stack
      - key: DB_CONNECTION
        value: pgsql
      - key: DB_HOST
        fromDatabase:
          name: school-db
          property: host
      - key: DB_PORT
        fromDatabase:
          name: school-db
          property: port
      - key: DB_DATABASE
        fromDatabase:
          name: school-db
          property: database
      - key: DB_USERNAME
        fromDatabase:
          name: school-db
          property: user
      - key: DB_PASSWORD
        fromDatabase:
          name: school-db
          property: password
      - key: SESSION_DRIVER
        value: database
      - key: CACHE_DRIVER
        value: file
      - key: QUEUE_CONNECTION
        value: sync

databases:
  - name: school-db
    plan: free
    databaseName: school_management
    user: school_user
```

### 1.3 Create .env.production File

Create a `.env.production` file for Render:

```env
APP_NAME="School Management System"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://your-app-name.onrender.com

LOG_CHANNEL=stack
LOG_LEVEL=error

# Database Configuration (Render PostgreSQL)
DB_CONNECTION=pgsql
DB_HOST=
DB_PORT=5432
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=

# Session Configuration
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

# Cache Configuration
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync

# Mail Configuration (Render SMTP)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="${APP_NAME}"

# Security
SESSION_SECURE_COOKIE=true
```

## Step 2: Deploy to Render

### 2.1 Create New Web Service
1. Go to [Render Dashboard](https://dashboard.render.com)
2. Click "New +" → "Web Service"
3. Connect your Git repository
4. Configure the service:
   - **Name**: school-management-system
   - **Environment**: PHP
   - **Plan**: Free
   - **Build Command**: `composer install --no-dev --optimize-autoloader`
   - **Start Command**: `php artisan serve --host=0.0.0.0 --port=$PORT`

### 2.2 Create Database
1. Go to "New +" → "PostgreSQL"
2. Configure the database:
   - **Name**: school-db
   - **Plan**: Free
   - **Database Name**: school_management
   - **User**: school_user

### 2.3 Configure Environment Variables
In your web service settings, add these environment variables:

```env
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:YOUR_GENERATED_KEY
DB_CONNECTION=pgsql
DB_HOST=your-db-host
DB_PORT=5432
DB_DATABASE=your-db-name
DB_USERNAME=your-db-user
DB_PASSWORD=your-db-password
SESSION_DRIVER=database
CACHE_DRIVER=file
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
```

## Step 3: Database Setup

### 3.1 Run Migrations
After deployment, you'll need to run migrations. You can do this via Render's shell:

1. Go to your web service
2. Click "Shell"
3. Run these commands:

```bash
php artisan migrate --force
php artisan db:seed
```

### 3.2 Create Admin User
```bash
php artisan tinker
```

```php
$user = new App\Models\User();
$user->name = 'Admin';
$user->email = 'admin@school.com';
$user->password = Hash::make('password123');
$user->user_type = 'admin';
$user->status = 'active';
$user->is_active = true;
$user->save();
```

## Step 4: Configure File Storage

### 4.1 Update Storage Configuration
Since Render doesn't support persistent file storage, you'll need to use cloud storage:

1. **For file uploads**, use AWS S3 or similar
2. **For logs**, use external logging service
3. **For sessions**, use database (already configured)

### 4.2 AWS S3 Configuration (Optional)
Add to your environment variables:

```env
AWS_ACCESS_KEY_ID=your-access-key
AWS_SECRET_ACCESS_KEY=your-secret-key
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=your-bucket-name
FILESYSTEM_DISK=s3
```

## Step 5: Email Configuration

### 5.1 Gmail SMTP Setup
1. Enable 2-factor authentication on Gmail
2. Generate an App Password
3. Use the app password in your environment variables

### 5.2 Alternative Email Services
- **SendGrid**: Professional email service
- **Mailgun**: Developer-friendly email API
- **Amazon SES**: AWS email service

## Step 6: Performance Optimization

### 6.1 Enable Caching
Add these environment variables:

```env
CACHE_DRIVER=redis
REDIS_URL=your-redis-url
SESSION_DRIVER=redis
```

### 6.2 Database Optimization
- Use connection pooling
- Enable query caching
- Optimize database indexes

## Step 7: Security Configuration

### 7.1 Environment Variables
- Never commit `.env` files to Git
- Use Render's environment variable management
- Rotate secrets regularly

### 7.2 HTTPS Configuration
- Render provides free SSL certificates
- Force HTTPS redirects
- Use secure cookies

## Step 8: Monitoring and Logging

### 8.1 Application Monitoring
- Use Render's built-in monitoring
- Set up uptime monitoring
- Monitor database performance

### 8.2 Error Tracking
- Integrate Sentry for error tracking
- Set up log aggregation
- Monitor application performance

## Step 9: Backup Strategy

### 9.1 Database Backups
- Render provides automatic database backups
- Set up additional backup strategies
- Test backup restoration

### 9.2 File Backups
- Use cloud storage for file persistence
- Implement automated backup scripts
- Store backups in multiple locations

## Step 10: Custom Domain (Optional)

### 10.1 Add Custom Domain
1. Go to your service settings
2. Click "Custom Domains"
3. Add your domain
4. Update DNS records

### 10.2 SSL Certificate
- Render provides free SSL certificates
- Automatic renewal
- Force HTTPS redirects

## Troubleshooting

### Common Issues

#### 1. Build Failures
- Check PHP version compatibility
- Verify Composer dependencies
- Check build logs for errors

#### 2. Database Connection Issues
- Verify database credentials
- Check network connectivity
- Ensure database is running

#### 3. File Upload Issues
- Use cloud storage for file persistence
- Check file size limits
- Verify storage permissions

#### 4. Email Not Sending
- Verify SMTP credentials
- Check firewall settings
- Test with different email providers

## Render-Specific Features

### 1. Automatic Deployments
- Deploy on every Git push
- Preview deployments for pull requests
- Rollback to previous versions

### 2. Environment Management
- Separate environments for staging/production
- Environment-specific configurations
- Secret management

### 3. Scaling
- Automatic scaling based on traffic
- Manual scaling options
- Resource monitoring

## Cost Optimization

### 1. Free Tier Limits
- 750 hours per month
- 1GB RAM
- 1GB storage

### 2. Upgrade Options
- Starter plan: $7/month
- Standard plan: $25/month
- Pro plan: $85/month

## Support and Resources

### 1. Render Documentation
- [Render Docs](https://render.com/docs)
- [PHP Deployment Guide](https://render.com/docs/deploy-php)
- [Database Guide](https://render.com/docs/databases)

### 2. Laravel on Render
- [Laravel Deployment](https://render.com/docs/deploy-laravel)
- [Best Practices](https://render.com/docs/laravel-best-practices)

## Conclusion

Your School Management System is now ready for deployment on Render! The platform provides:

- ✅ **Easy deployment** from Git repositories
- ✅ **Automatic scaling** based on traffic
- ✅ **Free SSL certificates** for security
- ✅ **Built-in monitoring** and logging
- ✅ **Database hosting** with automatic backups
- ✅ **Environment management** for different stages

The system will be accessible at `https://your-app-name.onrender.com` once deployed successfully.
