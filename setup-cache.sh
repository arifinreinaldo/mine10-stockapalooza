#!/bin/bash

# Stock Analysis App - Cache Directory Setup Script
# Run this after deployment to ensure cache directories exist with proper permissions

echo "🔧 Setting up cache directories for Stock Analysis App..."

# Navigate to Laravel root
cd "$(dirname "$0")"

# Create necessary cache directories
echo "📁 Creating cache directories..."
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs

# Set proper permissions (775 for directories, www-data group)
echo "🔐 Setting permissions..."
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# If running as root, change ownership to www-data
if [ "$EUID" -eq 0 ]; then
    echo "👤 Setting ownership to www-data..."
    chown -R www-data:www-data storage
    chown -R www-data:www-data bootstrap/cache
fi

# Clear and optimize cache
echo "🗑️  Clearing old cache..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Optimize for production
echo "⚡ Optimizing for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✅ Cache setup complete!"
echo ""
echo "📊 Cache directory status:"
ls -lah storage/framework/cache/
