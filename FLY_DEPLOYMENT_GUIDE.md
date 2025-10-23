# School Management System - Fly.io Deployment Guide

This comprehensive guide will help you deploy your Laravel School Management System to Fly.io using their free tier.

## 📋 Prerequisites

- A Fly.io account (sign up at [fly.io](https://fly.io))
- Git repository with your code
- PHP 8.2+ and Composer installed locally
- Node.js and npm installed locally

## 🚀 Quick Start (5 Minutes)

### Step 1: Install Fly CLI

**Windows (PowerShell):**
```powershell
iwr https://fly.io/install.ps1 -useb | iex
```

**macOS/Linux:**
```bash
curl -L https://fly.io/install.sh | sh
```

### Step 2: Login to Fly.io
```bash
fly auth login
```

### Step 3: Deploy Using Scripts

**Windows (PowerShell):**
```powershell
.\deploy-fly.ps1
```

**macOS/Linux:**
```bash
chmod +x deploy-fly.sh
./deploy-fly.sh
```

**Manual Deployment:**
```bash
# Initialize the app
fly launch --no-deploy

# Create volume for SQLite
fly volumes create data --size 1 --region iad

# Deploy
fly deploy
```

## 📁 Project Structure for Fly.io

The following files have been created/configured for Fly.io deployment:

```
SchoolManagementSystem/
├── Dockerfile              # Optimized for Fly.io
├── fly.toml               # Fly.io configuration
├── env.fly                # Environment variables for Fly.io
├── deploy-fly.sh          # Linux/macOS deployment script
├── deploy-fly.ps1         # Windows PowerShell deployment script
└── routes/health.php      # Health check endpoint
```

## 🔧 Configuration Details

### Dockerfile Features
- **Base Image**: PHP 8.3 with Alpine Linux (lightweight)
- **Database Support**: SQLite, PostgreSQL, MySQL
- **Extensions**: PDO, GD, BCMath, PCNTL, Exif
- **Frontend**: Node.js for asset compilation
- **Health Check**: Built-in health monitoring
- **Security**: Proper file permissions and user setup

### Fly.io Configuration (fly.toml)
- **App Name**: `school-management-system`
- **Region**: `iad` (Washington DC - good for US East Coast)
- **Resources**: 256MB RAM, 1 shared CPU (free tier)
- **Database**: SQLite with persistent volume
- **Auto-scaling**: Scales to 0 when idle, starts on demand
- **HTTPS**: Automatic SSL certificates

### Environment Variables
- **Database**: SQLite with persistent storage at `/database/`
- **Cache/Session**: File-based (no Redis needed for free tier)
- **Mail**: Log driver (emails logged to files)
- **Storage**: Local filesystem with symbolic links

## 💾 Database Setup

### SQLite Configuration
The system uses SQLite for the free tier deployment:
- **Location**: `/database/school_management.sqlite`
- **Persistence**: Volume mounted to prevent data loss
- **Size**: 1GB volume (expandable)

### Migration and Seeding
After deployment, the system automatically:
1. Runs database migrations
2. Seeds initial data (users, roles, permissions)
3. Creates necessary directories

## 🌐 Accessing Your Application

### URLs
- **Main Application**: `https://school-management-system.fly.dev`
- **Health Check**: `https://school-management-system.fly.dev/health`

### Default Login Credentials
After deployment, you can log in with:
- **Admin**: `admin@school.com` / `password`
- **Teacher**: `teacher@school.com` / `password`
- **Student**: `student@school.com` / `password`

## 📊 Monitoring and Management

### Useful Commands

```bash
# View application logs
fly logs --app school-management-system

# SSH into the application
fly ssh console --app school-management-system

# Check application status
fly status --app school-management-system

# Scale the application
fly scale count 1 --app school-management-system

# View environment variables
fly secrets list --app school-management-system

# Restart the application
fly apps restart school-management-system
```

### Health Monitoring
- **Endpoint**: `/health`
- **Checks**: Database connectivity, application status
- **Interval**: Every 30 seconds
- **Timeout**: 5 seconds

## 🔄 Updates and Maintenance

### Deploying Updates
```bash
# Deploy latest changes
fly deploy --app school-management-system

# Deploy with specific image
fly deploy --image your-image --app school-management-system
```

### Database Updates
```bash
# Run migrations
fly ssh console --app school-management-system -C "cd /var/www && php artisan migrate --force"

# Seed additional data
fly ssh console --app school-management-system -C "cd /var/www && php artisan db:seed --force"
```

### Backup Database
```bash
# Download database backup
fly ssh sftp get /database/school_management.sqlite ./backup.sqlite --app school-management-system
```

## 💰 Free Tier Limitations

### Resource Limits
- **VMs**: 3 shared VMs (256MB RAM each)
- **Bandwidth**: 160GB outbound per month
- **Storage**: 3GB total volume storage
- **CPU**: Shared CPU cores

### Scaling Behavior
- **Idle Apps**: Automatically scale to 0
- **Cold Start**: ~10-30 seconds to wake up
- **Auto-start**: Apps start on first request

### Recommended Optimizations
1. **Database**: Use SQLite for small to medium datasets
2. **Caching**: File-based caching is sufficient
3. **Sessions**: File-based sessions work well
4. **Storage**: Use local storage for file uploads

## 🚨 Troubleshooting

### Common Issues

#### 1. App Won't Start
```bash
# Check logs
fly logs --app school-management-system

# Check status
fly status --app school-management-system
```

#### 2. Database Connection Issues
```bash
# Check volume mount
fly volumes list --app school-management-system

# Verify database file
fly ssh console --app school-management-system -C "ls -la /database/"
```

#### 3. Permission Issues
```bash
# Fix storage permissions
fly ssh console --app school-management-system -C "cd /var/www && chmod -R 775 storage bootstrap/cache"
```

#### 4. Environment Variables
```bash
# Set missing variables
fly secrets set VARIABLE_NAME="value" --app school-management-system

# List all secrets
fly secrets list --app school-management-system
```

### Performance Optimization

#### 1. Enable Caching
```bash
fly ssh console --app school-management-system -C "cd /var/www && php artisan config:cache && php artisan route:cache && php artisan view:cache"
```

#### 2. Optimize Database
```bash
fly ssh console --app school-management-system -C "cd /var/www && php artisan optimize"
```

## 🔒 Security Considerations

### Environment Security
- All sensitive data stored as Fly.io secrets
- No hardcoded credentials in code
- HTTPS enforced automatically
- Secure session configuration

### Database Security
- SQLite file permissions restricted
- Database file not accessible via web
- Regular backups recommended

## 📈 Scaling Beyond Free Tier

When you need more resources:

### Upgrade Options
1. **Dedicated VMs**: More RAM and CPU
2. **PostgreSQL**: External database service
3. **Redis**: For caching and sessions
4. **CDN**: For static asset delivery

### Migration Commands
```bash
# Scale up resources
fly scale memory 512 --app school-management-system

# Add PostgreSQL
fly postgres create --name school-db

# Connect to PostgreSQL
fly postgres connect --app school-db
```

## 🆘 Support and Resources

### Fly.io Documentation
- [Fly.io Docs](https://fly.io/docs/)
- [Laravel on Fly.io](https://fly.io/docs/laravel/)
- [Free Tier Limits](https://fly.io/docs/about/pricing/)

### Application Support
- Check application logs: `fly logs --app school-management-system`
- SSH into app: `fly ssh console --app school-management-system`
- Health check: Visit `/health` endpoint

## ✅ Deployment Checklist

- [ ] Fly.io CLI installed and authenticated
- [ ] App created with `fly launch`
- [ ] Volume created for database persistence
- [ ] Environment variables configured
- [ ] Application deployed successfully
- [ ] Database migrations run
- [ ] Initial data seeded
- [ ] Health check passing
- [ ] Application accessible via HTTPS

## 🎉 Success!

Your School Management System is now running on Fly.io! 

**Access your application**: `https://school-management-system.fly.dev`

The system includes:
- ✅ Student management
- ✅ Teacher management  
- ✅ Grade management
- ✅ Fee management
- ✅ Attendance tracking
- ✅ Library management
- ✅ Transport management
- ✅ Hostel management
- ✅ Financial reporting
- ✅ User roles and permissions

Enjoy your deployed School Management System! 🚀
