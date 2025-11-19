# 📡 Stockapalooza API Documentation

Complete API reference for the Indonesian Stock Market Analyzer.

## Base URL

```
http://localhost:8000/api
```

## Authentication

Currently, no authentication is required. All endpoints are publicly accessible.

## Response Format

All responses follow this JSON structure:

```json
{
  "success": true|false,
  "data": {...}|[...],
  "message": "Optional message",
  "errors": {...}  // Only on validation errors
}
```

---

## Endpoints

### 1. Analyze Single Stock

Fetches and analyzes a single Indonesian stock with comprehensive metrics and recommendations.

**Endpoint:** `GET /api/analyze/{symbol}`

**Parameters:**
- `symbol` (path parameter) - Stock symbol (e.g., BBCA, BBRI, TLKM)
  - Can include or exclude .JK suffix
  - Case insensitive

**Example Request:**
```bash
curl http://localhost:8000/api/analyze/BBCA
```

**Example Response:**
```json
{
  "success": true,
  "data": {
    "symbol": "BBCA.JK",
    "name": "Bank Central Asia Tbk",
    "current_price": 9150,
    "score": 78.5,
    "max_score": 100,
    "recommendation": {
      "action": "BUY",
      "confidence": "Medium-High",
      "color": "success",
      "description": "Good opportunity. Several positive factors support buying."
    },
    "analysis": {
      "Fundamental Analysis": {
        "score": 85.5,
        "points": 17.1,
        "max_points": 20
      },
      "Technical Analysis": {
        "score": 72.0,
        "points": 14.4,
        "max_points": 20
      },
      "Valuation": {
        "score": 80.0,
        "points": 12.0,
        "max_points": 15
      },
      "Financial Health": {
        "score": 90.0,
        "points": 18.0,
        "max_points": 20
      },
      "Momentum & Liquidity": {
        "score": 70.0,
        "points": 7.0,
        "max_points": 10
      },
      "Dividend": {
        "score": 60.0,
        "points": 6.0,
        "max_points": 10
      }
    },
    "reasons": [
      {
        "type": "positive",
        "message": "P/E Ratio (12.5) is attractive (< 15), indicating potentially undervalued stock."
      },
      {
        "type": "positive",
        "message": "Excellent ROE: 18.5% (> 15%) shows efficient use of equity."
      }
    ],
    "metrics": {
      "price": {
        "current": 9150,
        "change": 50,
        "change_percent": 0.55,
        "day_range": [9100, 9200]
      },
      "valuation": {
        "pe_ratio": 12.5,
        "pb_ratio": 3.2,
        "market_cap": 1100000000000000
      },
      "profitability": {
        "eps": 732,
        "roe": 18.5,
        "profit_margin": 45.2
      },
      "dividend": {
        "yield": 2.5,
        "rate": 228.75
      }
    }
  }
}
```

**Error Response (404):**
```json
{
  "success": false,
  "message": "Unable to fetch data for symbol: INVALID.JK. Please check if the symbol is correct."
}
```

---

### 2. Analyze Multiple Stocks

Analyzes multiple stocks and returns them ranked by score.

**Endpoint:** `POST /api/analyze/multiple`

**Request Body:**
```json
{
  "symbols": ["BBCA", "BBRI", "TLKM", "ASII"]
}
```

**Validation Rules:**
- `symbols` - Required, array, minimum 1 stock, maximum 20 stocks
- Each symbol must be a string

**Example Request:**
```bash
curl -X POST http://localhost:8000/api/analyze/multiple \
  -H "Content-Type: application/json" \
  -d '{"symbols": ["BBCA", "BBRI", "TLKM"]}'
```

**Example Response:**
```json
{
  "success": true,
  "count": 3,
  "data": [
    {
      "symbol": "BBCA.JK",
      "score": 85.5,
      "recommendation": {...},
      ...
    },
    {
      "symbol": "BBRI.JK",
      "score": 78.2,
      "recommendation": {...},
      ...
    },
    {
      "symbol": "TLKM.JK",
      "score": 72.1,
      "recommendation": {...},
      ...
    }
  ]
}
```

**Error Response (400):**
```json
{
  "success": false,
  "errors": {
    "symbols": ["The symbols field is required."]
  }
}
```

---

### 3. Get Top Indonesian Stocks

Returns analysis of the top 10 Indonesian blue-chip stocks, ranked by score.

**Endpoint:** `GET /api/top-stocks`

**Default Stocks Analyzed:**
- BBCA - Bank Central Asia
- BBRI - Bank Rakyat Indonesia
- BMRI - Bank Mandiri
- TLKM - Telkom Indonesia
- ASII - Astra International
- UNVR - Unilever Indonesia
- BBNI - Bank Negara Indonesia
- GOTO - GoTo Gojek Tokopedia
- ICBP - Indofood CBP
- INDF - Indofood Sukses Makmur

**Example Request:**
```bash
curl http://localhost:8000/api/top-stocks
```

**Example Response:**
```json
{
  "success": true,
  "count": 10,
  "data": [
    {
      "symbol": "BBCA.JK",
      "name": "Bank Central Asia Tbk",
      "score": 85.5,
      "current_price": 9150,
      "recommendation": {...},
      "analysis": {...},
      "reasons": [...],
      "metrics": {...}
    },
    ...
  ],
  "updated_at": "2024-01-15 10:30:45"
}
```

---

### 4. Compare Stocks

Compares multiple stocks side-by-side with key metrics.

**Endpoint:** `POST /api/compare`

**Request Body:**
```json
{
  "symbols": ["BBCA", "BBRI"]
}
```

**Validation Rules:**
- `symbols` - Required, array, minimum 2 stocks, maximum 5 stocks

**Example Request:**
```bash
curl -X POST http://localhost:8000/api/compare \
  -H "Content-Type: application/json" \
  -d '{"symbols": ["BBCA", "BBRI", "BMRI"]}'
```

**Example Response:**
```json
{
  "success": true,
  "data": [
    {
      "symbol": "BBCA.JK",
      "name": "Bank Central Asia Tbk",
      "price": 9150,
      "change_percent": 0.55,
      "score": 85.5,
      "recommendation": "STRONG BUY",
      "pe_ratio": 12.5,
      "pb_ratio": 3.2,
      "dividend_yield": 2.5,
      "roe": 18.5,
      "market_cap": 1100000000000000
    },
    {
      "symbol": "BBRI.JK",
      "name": "Bank Rakyat Indonesia Tbk",
      "price": 4720,
      "change_percent": 1.08,
      "score": 78.2,
      "recommendation": "BUY",
      "pe_ratio": 8.9,
      "pb_ratio": 2.1,
      "dividend_yield": 3.2,
      "roe": 16.8,
      "market_cap": 590000000000000
    },
    {
      "symbol": "BMRI.JK",
      "name": "Bank Mandiri Tbk",
      "price": 6175,
      "change_percent": -0.32,
      "score": 75.8,
      "recommendation": "BUY",
      "pe_ratio": 9.2,
      "pb_ratio": 1.8,
      "dividend_yield": 4.1,
      "roe": 15.2,
      "market_cap": 615000000000000
    }
  ]
}
```

---

### 5. Get Stock Data (Without Analysis)

Fetches raw stock data without performing analysis.

**Endpoint:** `GET /api/stock/{symbol}`

**Example Request:**
```bash
curl http://localhost:8000/api/stock/BBCA
```

**Example Response:**
```json
{
  "success": true,
  "data": {
    "symbol": "BBCA.JK",
    "name": "Bank Central Asia Tbk",
    "currency": "IDR",
    "exchange": "JKT",
    "current_price": 9150,
    "previous_close": 9100,
    "open": 9125,
    "day_high": 9200,
    "day_low": 9100,
    "change": 50,
    "change_percent": 0.55,
    "volume": 12500000,
    "avg_volume": 15000000,
    "market_cap": 1100000000000000,
    "pe_ratio": 12.5,
    "forward_pe": 11.8,
    "pb_ratio": 3.2,
    "eps": 732,
    "dividend_yield": 0.025,
    "dividend_rate": 228.75,
    "beta": 1.05,
    "profit_margin": 0.452,
    "operating_margin": 0.512,
    "roe": 0.185,
    "roa": 0.032,
    "debt_to_equity": 45.2,
    "current_ratio": 1.25,
    "recommendation": "buy",
    "target_price": 10500,
    "fetched_at": "2024-01-15 10:30:45"
  }
}
```

---

### 6. Clear Cache

Clears cached data for a specific stock symbol.

**Endpoint:** `DELETE /api/cache/{symbol}`

**Example Request:**
```bash
curl -X DELETE http://localhost:8000/api/cache/BBCA
```

**Example Response:**
```json
{
  "success": true,
  "message": "Cache cleared for BBCA.JK"
}
```

---

## Data Types & Enums

### Recommendation Types
- `STRONG BUY` - Score 80-100
- `BUY` - Score 65-79
- `HOLD` - Score 50-64
- `CONSIDER SELLING` - Score 35-49
- `SELL` - Score 0-34

### Confidence Levels
- `High`
- `Medium-High`
- `Medium`
- `Medium-Low`
- `Low`

### Reason Types
- `positive` - Bullish indicator
- `negative` - Bearish indicator
- `neutral` - Neutral information
- `warning` - Caution indicator

### Recommendation Colors
- `success` - Green (Buy recommendations)
- `warning` - Yellow (Hold/neutral)
- `danger` - Red (Sell recommendations)

---

## Rate Limiting

Currently, there is no rate limiting implemented. However, data is cached for 5 minutes to reduce load on external APIs.

**Cache TTL:** 300 seconds (5 minutes)

---

## Error Codes

| HTTP Code | Meaning |
|-----------|---------|
| 200 | Success |
| 400 | Bad Request (validation error) |
| 404 | Stock Not Found |
| 500 | Server Error |

---

## Notes

1. **Stock Symbols**: All Indonesian stocks should use the `.JK` suffix (Jakarta Stock Exchange). The API will automatically add this if not provided.

2. **Caching**: Stock data is cached for 5 minutes. Use the cache clearing endpoint if you need fresh data immediately.

3. **Data Source**: All data comes from Yahoo Finance API, which provides real-time and historical data for Indonesian stocks.

4. **Analysis Algorithm**: The scoring system uses weighted analysis across 6 categories. See README.md for detailed methodology.

5. **Timeouts**: API requests have a 10-second timeout for external data fetching.

---

## Mobile Integration Example

### Flutter
```dart
import 'package:http/http.dart' as http;
import 'dart:convert';

Future<Map<String, dynamic>> analyzeStock(String symbol) async {
  final response = await http.get(
    Uri.parse('http://your-server.com/api/analyze/$symbol'),
  );

  if (response.statusCode == 200) {
    return json.decode(response.body);
  } else {
    throw Exception('Failed to analyze stock');
  }
}
```

### Kotlin (Android)
```kotlin
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.withContext
import okhttp3.OkHttpClient
import okhttp3.Request

suspend fun analyzeStock(symbol: String): String = withContext(Dispatchers.IO) {
    val client = OkHttpClient()
    val request = Request.Builder()
        .url("http://your-server.com/api/analyze/$symbol")
        .build()

    client.newCall(request).execute().use { response ->
        response.body?.string() ?: throw Exception("Empty response")
    }
}
```

---

## Support

For issues or questions, please refer to the main README.md or create an issue in the repository.
