# 🚀 Fly.io Deployment Files

This directory contains all necessary files to deploy the School Management System to Fly.io.

## 📁 Files Overview

| File | Purpose |
|------|---------|
| `Dockerfile` | Optimized container configuration for Fly.io |
| `fly.toml` | Fly.io application configuration |
| `env.fly` | Environment variables for production |
| `deploy-fly.sh` | Linux/macOS deployment script |
| `deploy-fly.ps1` | Windows PowerShell deployment script |
| `routes/health.php` | Health check endpoint |
| `FLY_DEPLOYMENT_GUIDE.md` | Comprehensive deployment guide |

## ⚡ Quick Deploy

### Windows
```powershell
.\deploy-fly.ps1
```

### Linux/macOS
```bash
chmod +x deploy-fly.sh
./deploy-fly.sh
```

### Manual Deploy
```bash
fly launch --no-deploy
fly volumes create data --size 1 --region iad
fly deploy
```

## 🔧 Key Features

- **Free Tier Optimized**: Uses 256MB RAM, shared CPU
- **SQLite Database**: Persistent storage with volume mount
- **Auto-scaling**: Scales to 0 when idle
- **Health Monitoring**: Built-in health checks
- **HTTPS**: Automatic SSL certificates
- **Laravel 11**: Full Laravel framework support

## 📖 Documentation

For detailed instructions, see [FLY_DEPLOYMENT_GUIDE.md](./FLY_DEPLOYMENT_GUIDE.md)

## 🆘 Support

If you encounter issues:
1. Check the deployment guide
2. Run `fly logs --app school-management-system`
3. Verify health check: `https://your-app.fly.dev/health`
