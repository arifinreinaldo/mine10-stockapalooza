#!/bin/bash

echo "🎯 Testing Dashboard Bullet Points Feature"
echo "=========================================="
echo ""

# Test with BBRI (BUMN Bank)
echo "📊 Test 1: BBRI.JK (BUMN Bank)"
echo "Expected bullets: Trend, Volatility, Liquidity, Non-Sharia, BUMN, Sector, Smart Money"
echo ""
curl -s "http://127.0.0.1:8000/api/analyze/BBRI.JK" | jq -r '
.metrics |
"🔍 BULLET POINTS THAT WILL APPEAR IN DASHBOARD:",
"",
"1. TREND (ADX):",
"   • Strength: \(.technical.adx.trend_strength)",
"   • Signal: \(.technical.adx.signal)",
"   • ADX Value: \(.technical.adx.adx)",
"",
"2. VOLATILITY (ATR):",
"   • Category: \(.technical.atr.volatility_category)",
"   • Daily Swings: \(.technical.atr.atr_percent)%",
"   • Position Size: \(.technical.atr.suggested_position_size)",
"",
"3. LIQUIDITY:",
"   • Score: \(.market_context.liquidity.score)/\(.market_context.liquidity.max_score)",
"   • Category: \(.market_context.liquidity.category)",
"",
"4. SHARIA COMPLIANCE:",
"   • Compliant: \(.market_context.sharia_compliance.is_compliant)",
"   • In DES List: \(.market_context.sharia_compliance.in_des_list)",
"",
"5. BUMN STATUS:",
"   • Is BUMN: \(.market_context.bumn_status.is_bumn)",
"   • Tier: \(.market_context.bumn_status.bumn_info.tier)",
"   • Govt Ownership: \(.market_context.bumn_status.bumn_info.ownership)%",
"",
"6. SECTOR ROTATION:",
"   • Sector: \(.market_context.sector_rotation.sector)",
"   • Status: \(.market_context.sector_rotation.sector_status)",
"   • 1-Month: \(.market_context.sector_rotation.momentum_1month_percent)%",
"",
"7. INSTITUTIONAL OWNERSHIP:",
"   • Institutional: \((.ownership.institutional_percent * 100) | round)%",
"   • Insider: \((.ownership.insider_percent * 100) | round)%"
'

echo ""
echo "=========================================="
echo ""
echo "📊 Test 2: GOTO.JK (Tech - High Volatility, Sharia)"
echo "Expected bullets: High volatility, Sharia compliant, Technology sector"
echo ""
curl -s "http://127.0.0.1:8000/api/analyze/GOTO.JK" | jq -r '
.metrics |
"🔍 KEY BULLETS:",
"• Volatility: \(.technical.atr.volatility_category) (\(.technical.atr.atr_percent)%)",
"• Sharia: \(.market_context.sharia_compliance.is_compliant)",
"• BUMN: \(.market_context.bumn_status.is_bumn)",
"• Sector: \(.market_context.sector_rotation.sector) - \(.market_context.sector_rotation.sector_status)"
'

echo ""
echo "=========================================="
echo ""
echo "✅ DASHBOARD ACCESS:"
echo "   http://localhost:8000/dashboard"
echo ""
echo "📝 To see the bullet points:"
echo "   1. Open http://localhost:8000/dashboard in your browser"
echo "   2. Search for BBRI, GOTO, or any IDX stock"
echo "   3. Look for '📋 Stock Snapshot' section in Decision Dashboard"
echo ""
