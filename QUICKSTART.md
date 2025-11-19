# 🚀 Quick Start Guide

Get Stockapalooza up and running in 5 minutes!

## Prerequisites Check

Before you begin, ensure you have:
- ✅ PHP 8.1 or higher (`php -v`)
- ✅ Composer (`composer -v`)
- ✅ Git

## Installation Steps

### 1. Clone & Navigate
```bash
git clone <your-repo-url>
cd stockapalooza
```

### 2. Install Dependencies
```bash
composer install
```

### 3. Environment Setup
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Set Permissions
```bash
chmod -R 775 storage bootstrap/cache
```

### 5. Start Server
```bash
php artisan serve
```

### 6. Open Browser
Navigate to: **http://localhost:8000**

## 🎉 You're Ready!

You should see the Stockapalooza interface. Try these:

1. **Quick Analysis**: Click "BBCA" quick-pick button
2. **Top Stocks**: Click "Top 10 Stocks" button
3. **Custom Search**: Type "GOTO" and click "Analyze Stock"

## 🧪 Test the API

```bash
# Test single stock analysis
curl http://localhost:8000/api/analyze/BBCA

# Test top stocks
curl http://localhost:8000/api/top-stocks

# Test comparison
curl -X POST http://localhost:8000/api/compare \
  -H "Content-Type: application/json" \
  -d '{"symbols": ["BBCA", "BBRI"]}'
```

## 📱 For Mobile Development

The API is ready to use! Here's a quick test:

```bash
# Get JSON response for your mobile app
curl http://localhost:8000/api/analyze/TLKM | jq
```

### Flutter Integration
```dart
final response = await http.get(
  Uri.parse('http://YOUR_IP:8000/api/analyze/BBCA')
);
final data = json.decode(response.body);
```

### Kotlin Integration
```kotlin
val client = OkHttpClient()
val request = Request.Builder()
    .url("http://YOUR_IP:8000/api/analyze/BBCA")
    .build()
val response = client.newCall(request).execute()
```

## 🔧 Configuration (Optional)

### Change Cache Duration
Edit `.env`:
```env
STOCK_CACHE_TTL=600  # 10 minutes
```

### Customize Stock List
Edit `config/stocks.php` to add/remove stocks from the default list.

## ⚠️ Troubleshooting

### "Class not found" error
```bash
composer dump-autoload
```

### Permission denied
```bash
sudo chmod -R 775 storage bootstrap/cache
sudo chown -R $USER:www-data storage bootstrap/cache
```

### Port 8000 already in use
```bash
php artisan serve --port=8080
```

### Can't connect from mobile device
Make sure to use your computer's IP address instead of localhost:
```bash
php artisan serve --host=0.0.0.0 --port=8000
# Then use http://YOUR_IP:8000 from your mobile device
```

Find your IP:
- **Linux/Mac**: `ifconfig | grep inet`
- **Windows**: `ipconfig`

## 📚 Next Steps

1. Read the full [README.md](README.md) for detailed features
2. Check [API_DOCUMENTATION.md](API_DOCUMENTATION.md) for complete API reference
3. Start building your mobile app using the API endpoints!

## 🤝 Need Help?

- Check the documentation files
- Review the code comments
- Create an issue in the repository

---

**Happy Trading! 📈**
