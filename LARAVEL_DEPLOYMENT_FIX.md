# Quick Fly.io Deployment Guide - Laravel Fix

## 🚨 Problem Solved: Laravel Detection Conflict

The error "launch manifest was created for a app, but this is a Laravel app" occurs because Fly.io tries to auto-detect and configure Laravel apps, but conflicts with our existing `fly.toml`.

## ✅ Solution: Manual Deployment Steps

### Step 1: Create App Manually
```bash
# Create the app without auto-detection
fly apps create school-management-system --org personal
```

### Step 2: Create Volume
```bash
# Create persistent volume for SQLite
fly volumes create data --size 1 --region iad --app school-management-system
```

### Step 3: Set Environment Variables
```bash
# Set required environment variables
fly secrets set APP_KEY="$(php artisan key:generate --show)" --app school-management-system
fly secrets set APP_URL="https://school-management-system.fly.dev" --app school-management-system
```

### Step 4: Deploy
```bash
# Deploy the application
fly deploy --app school-management-system
```

### Step 5: Initialize Database
```bash
# Run migrations
fly ssh console --app school-management-system -C "cd /var/www && php artisan migrate --force"

# Seed initial data
fly ssh console --app school-management-system -C "cd /var/www && php artisan db:seed --force"

# Create storage link
fly ssh console --app school-management-system -C "cd /var/www && php artisan storage:link"
```

## 🎯 Alternative: One-Command Fix

If you want to use the automated script, first delete any existing app:

```bash
# Remove existing app if it exists
fly apps destroy school-management-system --yes

# Then run the deployment script
.\deploy-fly.ps1
```

## 🔍 Why This Happens

Fly.io's `fly launch` command:
1. Detects Laravel framework
2. Tries to auto-generate configuration
3. Conflicts with our existing `fly.toml`
4. Fails with manifest error

Our solution bypasses the auto-detection by using `fly apps create` instead.

## ✅ Expected Result

After successful deployment:
- **URL**: `https://school-management-system.fly.dev`
- **Health Check**: `https://school-management-system.fly.dev/health`
- **Database**: SQLite with persistent storage
- **Features**: All Laravel functionality working
