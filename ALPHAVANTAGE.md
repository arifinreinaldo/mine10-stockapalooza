# Alpha Vantage Integration (Optional)

The stock analysis system now supports **optional** fundamental data enrichment via Alpha Vantage API.

## How It Works

1. **Without Alpha Vantage**: System uses **adaptive technical-only scoring** (works perfectly without API key)
2. **With Alpha Vantage**: System enriches missing Yahoo Finance fundamental data with Alpha Vantage

## Setup (Optional)

### Step 1: Get Free API Key

1. Visit: https://www.alphavantage.co/support/#api-key
2. Sign up for a free API key (25 requests/day limit)

### Step 2: Configure

Add to your `.env` file:

```env
ALPHAVANTAGE_API_KEY=your_api_key_here
```

### Step 3: Restart

```bash
php artisan octane:reload
php artisan cache:clear
```

## When To Use

### ✅ **Recommended For:**
- **US Stocks** (AAPL, MSFT, GOOGL, etc.) - Excellent coverage
- Stocks where Yahoo Finance returns no fundamental data
- When you need P/E, P/B, EPS, ROE, Market Cap data

### ❌ **Not Recommended For:**
- **IDX Stocks** (BBCA, TLKM, etc.) - Limited coverage
- **SGX Stocks** - Limited coverage
- High-frequency scanning (rate limits)

## Rate Limits

| Plan | Limit | Best For |
|------|-------|----------|
| Free | 25 requests/day | Testing, US stocks only |
| Premium | 75-1200/day | Production use |

## System Behavior

### Without API Key:
```json
{
  "score": 25.5,
  "scoring_mode": "technical",
  "fundamental_source": "yahoo_finance"
}
```

### With API Key (enriched):
```json
{
  "score": 68.5,
  "scoring_mode": "hybrid",
  "fundamental_source": "alpha_vantage",
  "pe_ratio": 28.5,
  "eps": 6.42
}
```

## Testing

Check if configured:

```bash
curl http://localhost:8080/api/dashboard/AAPL?market=us | jq '.data.stock_info.fundamental_source'
```

Response:
- `"yahoo_finance"` - Alpha Vantage not configured or not enriched
- `"alpha_vantage"` - Successfully enriched with Alpha Vantage data

## Performance Impact

- ✅ **No impact if not configured** - System works normally
- ✅ **Minimal impact when configured** - 24-hour cache per stock
- ⚠️ **First request may be slower** (~1-2s extra for Alpha Vantage call)

## Troubleshooting

### Issue: "Rate limit exceeded"
**Solution**: Reduce scanning frequency or upgrade to premium plan

### Issue: "Stock not found"
**Solution**: Alpha Vantage may not have data for non-US stocks. System will fall back to technical-only scoring.

### Issue: "Permission denied"
**Solution**:
```bash
chmod 644 app/Services/AlphaVantageService.php
chown www-data:www-data app/Services/AlphaVantageService.php
```

## Alternative Data Sources

You can also implement other providers by creating similar services:
- Financial Modeling Prep
- IEX Cloud
- Twelve Data
- Polygon.io

Just follow the same pattern as `AlphaVantageService.php`.
