# 🤖 Stock Analysis ML Scripts

Free machine learning tools to analyze your stock data!

## 📋 What's Included

### 1. Feature Importance (`feature_importance.py`)
**What it does:** Finds which stock indicators MATTER MOST for predicting price increases!

- Analyzes RSI, PE ratio, volume, accumulation, etc.
- Shows which metrics predict 5% gains best
- Uses XGBoost (industry-standard ML)

**Example output:**
```
🏆 FEATURE IMPORTANCE RANKINGS:
==================================================
accumulation_strength     ████████████████ 0.245
institutional_percent     ████████████ 0.189
rsi                       ██████████ 0.156
volume_ratio              ███████ 0.112
swing_score               ████ 0.089
```

### 2. Stock Clustering (`stock_clustering.py`)
**What it does:** Groups stocks that behave SIMILARLY! Find twins in the market.

- Creates 5 clusters based on behavior patterns
- If you like BBCA, suggests similar stocks
- Finds Blue Chips, Growth Stocks, Undervalued Gems

**Example output:**
```
🏷️  Cluster 0: 🌟 Blue Chip Stars (Strong + Institutional)
   Stocks (3): BBCA, BBRI, BMRI
   Avg Score: 78.5
   Avg Institutional: 65.2%

🏷️  Cluster 1: 💎 Undervalued Gems (Low RSI)
   Stocks (4): TLKM, ASII, UNVR, GGRM
   Avg Score: 58.3
   Avg RSI: 32.5
```

## 🚀 Quick Start

### Step 1: Install Python Libraries
```bash
pip install -r requirements.txt
```

### Step 2: Collect Data
Use the dashboard to analyze stocks. Data is automatically saved!

### Step 3: Run ML Scripts

**Find important features:**
```bash
python3 ml_scripts/feature_importance.py
```

**Cluster similar stocks:**
```bash
python3 ml_scripts/stock_clustering.py
```

## 📊 How Much Data Do You Need?

| Script | Minimum Data | Recommended |
|--------|--------------|-------------|
| Feature Importance | 10+ stocks analyzed 7+ days ago | 50+ stocks |
| Stock Clustering | 5+ stocks | 20+ stocks |

**💡 Tip:** The more stocks you analyze, the smarter the ML gets!

## 🎯 What ML Can Tell You

✅ **Which indicators predict success** - RSI? PE ratio? Volume? Institutional buying?
✅ **Stock "personalities"** - Growth, Value, Blue Chip, Risky
✅ **Similar stocks** - "If you like X, try Y"
✅ **Pattern detection** - What traits do winning stocks share?

## ⚠️ Important Notes

1. **Data Collection Takes Time**
   - Feature importance needs 7+ day old data
   - Start analyzing stocks TODAY for results next week!

2. **More Data = Smarter AI**
   - 10 stocks = basic insights
   - 50+ stocks = powerful predictions
   - 200+ stocks = professional-grade ML

3. **This is FREE!**
   - No API costs
   - No cloud fees
   - Runs on your machine
   - All libraries are open-source

## 🔮 Future Enhancements

Want more? These are easy to add:

- **Price Prediction**: Predict tomorrow's price
- **Buy/Sell Signals**: ML-powered recommendations
- **Risk Scoring**: Volatility predictions
- **Sector Analysis**: Best performing sectors

## 🤝 How It Works

```
User Analyzes Stock → Data Saved to Database → ML Scripts Read Database →
Insights Generated → Results Saved as JSON → Can be shown in Dashboard!
```

All data stays on YOUR machine. No external services needed!

## 📚 Next Steps

1. Start analyzing stocks daily
2. Run ML scripts weekly
3. Compare results over time
4. Discover patterns that work!

---

**Made with ❤️ using:**
- pandas (data manipulation)
- scikit-learn (clustering)
- XGBoost (feature importance)
- 100% FREE and open-source!
