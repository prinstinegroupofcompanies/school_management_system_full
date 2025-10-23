#!/bin/bash

# School Management System - Fly.io Deployment Script
# This script automates the deployment process to Fly.io

set -e  # Exit on any error

echo "🚀 Starting School Management System deployment to Fly.io..."

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check if flyctl is installed
if ! command -v fly &> /dev/null; then
    print_error "flyctl is not installed. Please install it first:"
    echo "  Windows: https://fly.io/docs/hands-on/install-flyctl/"
    echo "  Or run: iwr https://fly.io/install.ps1 -useb | iex"
    exit 1
fi

# Check if user is logged in to Fly.io
if ! fly auth whoami &> /dev/null; then
    print_warning "You are not logged in to Fly.io. Please log in first:"
    echo "  fly auth login"
    exit 1
fi

print_status "Checking if app already exists..."
if fly apps list | grep -q "school-management-system"; then
    print_warning "App 'school-management-system' already exists."
    read -p "Do you want to continue with deployment? (y/N): " -n 1 -r
    echo
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        print_status "Deployment cancelled."
        exit 0
    fi
else
    print_status "Creating new Fly.io app..."
    # Use apps create to avoid conflicts with Laravel detection
    fly apps create school-management-system --org personal
fi

print_status "Creating volume for SQLite database..."
if ! fly volumes list | grep -q "data"; then
    fly volumes create data --size 1 --region iad
    print_success "Volume 'data' created successfully"
else
    print_warning "Volume 'data' already exists"
fi

print_status "Setting environment variables..."
fly secrets set APP_KEY="$(php artisan key:generate --show)" --app school-management-system
fly secrets set APP_URL="https://school-management-system.fly.dev" --app school-management-system

print_status "Deploying application..."
fly deploy --app school-management-system

print_status "Running database migrations..."
fly ssh console --app school-management-system -C "cd /var/www && php artisan migrate --force"

print_status "Seeding database with initial data..."
fly ssh console --app school-management-system -C "cd /var/www && php artisan db:seed --force"

print_status "Creating storage link..."
fly ssh console --app school-management-system -C "cd /var/www && php artisan storage:link"

print_success "🎉 Deployment completed successfully!"
print_status "Your application is available at: https://school-management-system.fly.dev"
print_status "Health check: https://school-management-system.fly.dev/health"

echo
print_status "Useful commands:"
echo "  View logs: fly logs --app school-management-system"
echo "  SSH into app: fly ssh console --app school-management-system"
echo "  Scale app: fly scale count 1 --app school-management-system"
echo "  View app info: fly status --app school-management-system"
