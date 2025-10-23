# School Management System - Fly.io Deployment Script (PowerShell)
# This script automates the deployment process to Fly.io on Windows

param(
    [switch]$SkipConfirmation
)

# Set error action preference
$ErrorActionPreference = "Stop"

Write-Host "🚀 Starting School Management System deployment to Fly.io..." -ForegroundColor Blue

# Function to print colored output
function Write-Status {
    param([string]$Message)
    Write-Host "[INFO] $Message" -ForegroundColor Blue
}

function Write-Success {
    param([string]$Message)
    Write-Host "[SUCCESS] $Message" -ForegroundColor Green
}

function Write-Warning {
    param([string]$Message)
    Write-Host "[WARNING] $Message" -ForegroundColor Yellow
}

function Write-Error {
    param([string]$Message)
    Write-Host "[ERROR] $Message" -ForegroundColor Red
}

# Check if flyctl is installed
try {
    $null = Get-Command fly -ErrorAction Stop
    Write-Status "flyctl is installed"
} catch {
    Write-Error "flyctl is not installed. Please install it first:"
    Write-Host "  Run: iwr https://fly.io/install.ps1 -useb | iex" -ForegroundColor Yellow
    exit 1
}

# Check if user is logged in to Fly.io
try {
    $null = fly auth whoami 2>$null
    Write-Status "Logged in to Fly.io"
} catch {
    Write-Warning "You are not logged in to Fly.io. Please log in first:"
    Write-Host "  fly auth login" -ForegroundColor Yellow
    exit 1
}

Write-Status "Checking if app already exists..."
$appExists = $false
try {
    $apps = fly apps list 2>$null
    if ($apps -match "school-management-system") {
        $appExists = $true
        Write-Warning "App 'school-management-system' already exists."
    }
} catch {
    Write-Status "App does not exist, will create new one"
}

if ($appExists -and -not $SkipConfirmation) {
    $response = Read-Host "Do you want to continue with deployment? (y/N)"
    if ($response -notmatch "^[Yy]$") {
        Write-Status "Deployment cancelled."
        exit 0
    }
}

if (-not $appExists) {
    Write-Status "Creating new Fly.io app..."
    # Use --no-launch to avoid conflicts with existing fly.toml
    fly apps create school-management-system --org personal
}

Write-Status "Creating volume for SQLite database..."
try {
    $volumes = fly volumes list 2>$null
    if ($volumes -notmatch "data") {
        fly volumes create data --size 1 --region iad
        Write-Success "Volume 'data' created successfully"
    } else {
        Write-Warning "Volume 'data' already exists"
    }
} catch {
    Write-Error "Failed to create volume: $_"
    exit 1
}

Write-Status "Setting environment variables..."
try {
    # Generate APP_KEY
    $appKey = php artisan key:generate --show
    fly secrets set APP_KEY="$appKey" --app school-management-system
    fly secrets set APP_URL="https://school-management-system.fly.dev" --app school-management-system
    Write-Success "Environment variables set"
} catch {
    Write-Error "Failed to set environment variables: $_"
    exit 1
}

Write-Status "Deploying application..."
try {
    fly deploy --app school-management-system
    Write-Success "Application deployed successfully"
} catch {
    Write-Error "Deployment failed: $_"
    exit 1
}

Write-Status "Running database migrations..."
try {
    fly ssh console --app school-management-system -C "cd /var/www && php artisan migrate --force"
    Write-Success "Database migrations completed"
} catch {
    Write-Warning "Database migrations failed: $_"
}

Write-Status "Seeding database with initial data..."
try {
    fly ssh console --app school-management-system -C "cd /var/www && php artisan db:seed --force"
    Write-Success "Database seeding completed"
} catch {
    Write-Warning "Database seeding failed: $_"
}

Write-Status "Creating storage link..."
try {
    fly ssh console --app school-management-system -C "cd /var/www && php artisan storage:link"
    Write-Success "Storage link created"
} catch {
    Write-Warning "Storage link creation failed: $_"
}

Write-Success "🎉 Deployment completed successfully!"
Write-Status "Your application is available at: https://school-management-system.fly.dev"
Write-Status "Health check: https://school-management-system.fly.dev/health"

Write-Host ""
Write-Status "Useful commands:"
Write-Host "  View logs: fly logs --app school-management-system" -ForegroundColor Cyan
Write-Host "  SSH into app: fly ssh console --app school-management-system" -ForegroundColor Cyan
Write-Host "  Scale app: fly scale count 1 --app school-management-system" -ForegroundColor Cyan
Write-Host "  View app info: fly status --app school-management-system" -ForegroundColor Cyan
