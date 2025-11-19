# 📈 Stockapalooza - Indonesian Stock Market Analyzer

A comprehensive stock analysis system for the Indonesian Stock Exchange (IDX) built with Laravel. This application provides intelligent stock recommendations based on fundamental analysis, technical indicators, financial health metrics, and market momentum.

## 🌟 Features

### Multi-Factor Analysis
- **Fundamental Analysis**: P/E ratio, P/B ratio, EPS evaluation
- **Technical Analysis**: RSI, Moving Averages (SMA 20, SMA 50), Price Momentum
- **Financial Health**: ROE, Profit Margins, Debt-to-Equity ratios
- **Valuation Metrics**: Analyst recommendations, target price analysis
- **Dividend Analysis**: Yield evaluation for income investors
- **Liquidity Assessment**: Volume analysis and market capitalization

### Intelligent Scoring System
- Weighted scoring across 6 categories (total 100 points)
- Clear recommendations: Strong Buy, Buy, Hold, Consider Selling, Sell
- Detailed reasoning for each recommendation
- Color-coded confidence levels

### User-Friendly Interface
- Modern, responsive web interface
- Real-time stock analysis
- Top 10 Indonesian stocks ranking
- Quick-pick buttons for popular stocks
- Detailed breakdown of analysis categories
- Visual score indicators and progress bars

### RESTful API
- Analyze single stock
- Analyze multiple stocks with ranking
- Compare stocks side-by-side
- Get top Indonesian stocks
- Cache management

## 📊 Analysis Methodology

### Scoring Breakdown (Total: 100 points)

1. **Fundamental Analysis (20%)**: Evaluates P/E ratio, P/B ratio, and EPS
2. **Technical Analysis (25%)**: Analyzes RSI, moving averages, and momentum
3. **Valuation (15%)**: Compares current price to analyst targets
4. **Financial Health (20%)**: Assesses ROE, profit margins, and debt levels
5. **Momentum & Liquidity (10%)**: Examines volume and market cap
6. **Dividend (10%)**: Evaluates dividend yield for income potential

### Recommendation Levels

| Score Range | Recommendation | Description |
|-------------|---------------|-------------|
| 80-100 | **STRONG BUY** | Excellent opportunity with multiple positive indicators |
| 65-79 | **BUY** | Good opportunity with several supporting factors |
| 50-64 | **HOLD** | Mixed signals, wait for better entry point |
| 35-49 | **CONSIDER SELLING** | Several concerning factors detected |
| 0-34 | **SELL** | Multiple negative indicators, exit recommended |

## 🚀 Installation

### Prerequisites
- PHP 8.1 or higher
- Composer
- Web server (Apache/Nginx) or Laravel's built-in server

### Setup Instructions

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd stockapalooza
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Configure environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configure storage permissions**
   ```bash
   chmod -R 775 storage bootstrap/cache
   ```

5. **Start the development server**
   ```bash
   php artisan serve
   ```

6. **Access the application**
   - Web Interface: http://localhost:8000
   - API Endpoint: http://localhost:8000/api

## 📡 API Endpoints

### 1. Analyze Single Stock
```http
GET /api/analyze/{symbol}
```

**Example:**
```bash
curl http://localhost:8000/api/analyze/BBCA
```

**Response:**
```json
{
  "success": true,
  "data": {
    "symbol": "BBCA.JK",
    "name": "Bank Central Asia Tbk",
    "current_price": 9150,
    "score": 78.5,
    "recommendation": {
      "action": "BUY",
      "confidence": "Medium-High",
      "description": "Good opportunity. Several positive factors support buying."
    },
    "analysis": {...},
    "reasons": [...],
    "metrics": {...}
  }
}
```

### 2. Analyze Multiple Stocks
```http
POST /api/analyze/multiple
Content-Type: application/json

{
  "symbols": ["BBCA", "BBRI", "TLKM"]
}
```

### 3. Get Top Indonesian Stocks
```http
GET /api/top-stocks
```

Returns analysis of top 10 Indonesian blue-chip stocks, ranked by score.

### 4. Compare Stocks
```http
POST /api/compare
Content-Type: application/json

{
  "symbols": ["BBCA", "BBRI"]
}
```

### 5. Get Stock Data (No Analysis)
```http
GET /api/stock/{symbol}
```

### 6. Clear Cache
```http
DELETE /api/cache/{symbol}
```

## 🏦 Supported Stocks

The system works with all Indonesian stocks listed on IDX. Simply use the stock symbol (with or without .JK suffix).

### Popular Indonesian Stocks:

**Banking Sector:**
- BBCA - Bank Central Asia
- BBRI - Bank Rakyat Indonesia
- BMRI - Bank Mandiri
- BBNI - Bank Negara Indonesia

**Technology & Telecom:**
- GOTO - GoTo Gojek Tokopedia
- TLKM - Telkom Indonesia
- EXCL - XL Axiata
- ISAT - Indosat

**Consumer Goods:**
- UNVR - Unilever Indonesia
- ICBP - Indofood CBP
- INDF - Indofood Sukses Makmur
- MYOR - Mayora Indah

**Industrial & Infrastructure:**
- ASII - Astra International
- JSMR - Jasa Marga
- WIKA - Wijaya Karya

**Mining & Energy:**
- ANTM - Aneka Tambang
- ADRO - Adaro Energy
- PTBA - Bukit Asam

## 🎯 Usage Examples

### Web Interface

1. **Quick Analysis**: Click on any quick-pick button (BBCA, BBRI, etc.)
2. **Custom Stock**: Enter any IDX symbol and click "Analyze Stock"
3. **Top Stocks**: Click "Top 10 Stocks" to see ranked analysis of blue chips

### Programmatic Usage

```php
use App\Services\StockDataFetcher;
use App\Services\StockAnalyzer;

// Fetch stock data
$fetcher = new StockDataFetcher();
$stockData = $fetcher->fetchStockData('BBCA.JK');

// Analyze stock
$analyzer = new StockAnalyzer();
$analysis = $analyzer->analyze($stockData);

// Get recommendation
echo $analysis['recommendation']['action']; // e.g., "BUY"
echo $analysis['score']; // e.g., 78.5
```

### Command Line (using curl)

```bash
# Analyze BBCA
curl http://localhost:8000/api/analyze/BBCA

# Get top 10 stocks
curl http://localhost:8000/api/top-stocks

# Compare multiple stocks
curl -X POST http://localhost:8000/api/compare \
  -H "Content-Type: application/json" \
  -d '{"symbols": ["BBCA", "BBRI", "BMRI"]}'
```

## 🔧 Configuration

### Stock Analysis Weights
Edit `config/stocks.php` to adjust analysis weights:

```php
'analysis_weights' => [
    'fundamental' => 20,      // P/E, P/B, EPS
    'technical' => 25,        // RSI, Moving Averages
    'valuation' => 15,        // Target price, Recommendations
    'financial_health' => 20, // ROE, Profit Margin, Debt
    'momentum' => 10,         // Volume, Market Cap
    'dividend' => 10,         // Dividend Yield
],
```

### Cache Settings
Adjust cache TTL in `.env`:
```env
STOCK_CACHE_TTL=300  # 5 minutes (in seconds)
```

## 📱 Mobile Application (Future Development)

This Laravel backend provides a RESTful API that can be consumed by:
- **Flutter** mobile app
- **Kotlin Compose** Android app
- Any frontend framework (React, Vue, Angular)

The API returns JSON responses that are ready for mobile consumption.

## 🔐 Data Sources

- **Yahoo Finance API**: Primary data source for Indonesian stocks
- Stock symbols use `.JK` suffix for Jakarta Stock Exchange
- Data includes real-time prices, historical data, and fundamental metrics
- Data is cached for 5 minutes to optimize performance

## ⚠️ Disclaimer

**IMPORTANT**: This tool is for educational and informational purposes only. It should NOT be used as the sole basis for investment decisions.

- Stock market investments carry inherent risks
- Past performance does not guarantee future results
- Always do your own research (DYOR)
- Consult with licensed financial advisors before making investment decisions
- The creators are not responsible for any financial losses

## 🛠️ Technical Stack

- **Backend**: Laravel 10 (PHP 8.1+)
- **HTTP Client**: Guzzle
- **Data Source**: Yahoo Finance API
- **Frontend**: HTML5, CSS3, Vanilla JavaScript
- **Caching**: Laravel Cache (File/Redis)
- **Architecture**: RESTful API with MVC pattern

## 📝 Code Structure

```
stockapalooza/
├── app/
│   ├── Http/Controllers/
│   │   └── StockAnalysisController.php  # API endpoints
│   └── Services/
│       ├── StockDataFetcher.php         # Data fetching logic
│       └── StockAnalyzer.php            # Analysis algorithms
├── config/
│   └── stocks.php                        # Stock configurations
├── routes/
│   ├── api.php                          # API routes
│   └── web.php                          # Web routes
├── resources/views/
│   └── stock-analyzer.blade.php         # Web interface
└── public/
    └── index.php                        # Entry point
```

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## 📄 License

This project is open-sourced software licensed under the MIT license.

## 👨‍💻 Author

Created by a stock trader who codes - combining financial analysis expertise with software development skills.

## 🔮 Future Enhancements

- [ ] Machine learning predictions
- [ ] Portfolio management
- [ ] Real-time WebSocket updates
- [ ] Advanced charting with TradingView
- [ ] Email/SMS alerts for price targets
- [ ] Historical backtesting
- [ ] Mobile apps (Flutter/Kotlin Compose)
- [ ] Social sentiment analysis
- [ ] Multi-currency support
- [ ] Export to PDF/Excel

## 📞 Support

For issues, questions, or suggestions, please create an issue in the repository.

---

**Happy Trading! 📈💰**

Remember: The best investment you can make is in yourself. Learn continuously, invest wisely, and never risk more than you can afford to lose.
