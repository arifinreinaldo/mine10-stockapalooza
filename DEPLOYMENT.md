# Stock Analysis App - Deployment Guide

## Quick Setup (After Git Pull)

Run this script to set up cache directories and permissions:

```bash
./setup-cache.sh
```

Or manually:

```bash
# Create cache directories
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs

# Set permissions
chmod -R 775 storage
chmod -R 775 bootstrap/cache
chown -R www-data:www-data storage
chown -R www-data:www-data bootstrap/cache

# Clear and optimize
php artisan cache:clear
php artisan config:cache
php artisan route:cache
```

## Common Errors & Fixes

### Error: "Failed to open stream: No such file or directory"
**Cause**: Cache directories don't exist or have wrong permissions

**Fix**:
```bash
./setup-cache.sh
```

### Error: "DivisionByZeroError"
**Cause**: Stock data has zero/null values (already fixed in latest version)

**Fix**: Pull latest code from branch `claude/stock-analysis-matrix-01Q5ck68jsW77xEX1a2Uk54E`

## Environment Setup

1. **Copy environment file:**
   ```bash
   cp .env.example .env
   ```

2. **Generate app key:**
   ```bash
   php artisan key:generate
   ```

3. **Run database migrations:**
   ```bash
   php artisan migrate
   ```

4. **Install dependencies:**
   ```bash
   composer install --no-dev --optimize-autoloader
   npm install && npm run build
   ```

## Performance Optimization

The app uses 3-hour caching for stock scans. To force refresh cache:

```bash
php artisan cache:clear
```

Or use the dashboard "Force Refresh" button.

## Server Requirements

- PHP 8.1+
- Composer
- SQLite or MySQL
- 512MB RAM minimum (1GB+ recommended for scanning 200+ stocks)

## Features

- **Preset Scan**: 15 curated stocks (fast, 3-hour cache)
- **Comprehensive Scan**: ALL 200+ Indonesian stocks (slower, no cache)
- **Professional Indicators**: MACD, Stochastic, MFI, RSI Divergence, 52-week context
- **ML Ready**: Auto-saves analysis data for machine learning

## Support

For issues, check:
1. Cache directories exist and are writable
2. PHP version is 8.1+
3. All composer dependencies installed
4. .env file configured correctly
