#!/bin/bash

echo "======================================"
echo "Phase 1 Indicators - Quick Test"
echo "======================================"
echo ""

# Test 1: Check if classes load
echo "✓ Test 1: Checking if code compiles..."
php -l app/Services/StockAnalyzer.php
php -l app/Services/StockDataFetcher.php

echo ""
echo "✓ Test 2: Testing with BBCA stock..."
php artisan tinker --execute="
\$fetcher = app(App\Services\StockDataFetcher::class);
\$data = \$fetcher->fetchStockData('BBCA.JK');
if (\$data) {
    echo '✓ Stock data fetched successfully' . PHP_EOL;
    echo '  Price: ' . \$data['current_price'] . PHP_EOL;
    echo '  Has highs: ' . (count(\$data['historical_highs'] ?? []) > 0 ? 'YES' : 'NO') . PHP_EOL;
    echo '  Has lows: ' . (count(\$data['historical_lows'] ?? []) > 0 ? 'YES' : 'NO') . PHP_EOL;
    echo '  Institutional: ' . ((\$data['held_percent_institutions'] ?? 0) * 100) . '%' . PHP_EOL;
} else {
    echo '✗ Failed to fetch data' . PHP_EOL;
}
"

echo ""
echo "✓ Test 3: Testing analyzer with new indicators..."
php artisan tinker --execute="
\$fetcher = app(App\Services\StockDataFetcher::class);
\$analyzer = new App\Services\StockAnalyzer();
\$data = \$fetcher->fetchStockData('BBCA.JK');
if (\$data) {
    \$analysis = \$analyzer->analyze(\$data);
    echo '✓ Analysis completed' . PHP_EOL;
    echo '  Score: ' . \$analysis['score'] . '/100' . PHP_EOL;
    echo '  Recommendation: ' . \$analysis['recommendation']['action'] . PHP_EOL;

    \$categories = array_keys(\$analysis['analysis']);
    echo '  Categories (' . count(\$categories) . '): ' . PHP_EOL;
    foreach (\$categories as \$cat) {
        echo '    - ' . \$cat . PHP_EOL;
    }

    // Check for new categories
    \$hasRisk = isset(\$analysis['analysis']['Risk Assessment']);
    \$hasFlow = isset(\$analysis['analysis']['Market Participation']);

    echo PHP_EOL;
    echo '  New categories found:' . PHP_EOL;
    echo '    Risk Assessment: ' . (\$hasRisk ? '✓ YES' : '✗ NO') . PHP_EOL;
    echo '    Market Participation: ' . (\$hasFlow ? '✓ YES' : '✗ NO') . PHP_EOL;

    // Check for new indicators
    if (isset(\$analysis['metrics']['technical']['adx'])) {
        \$adx = \$analysis['metrics']['technical']['adx'];
        echo '    ADX: ✓ ' . \$adx['adx'] . ' (' . \$adx['signal'] . ')' . PHP_EOL;
    }
    if (isset(\$analysis['metrics']['technical']['atr'])) {
        \$atr = \$analysis['metrics']['technical']['atr'];
        echo '    ATR: ✓ ' . \$atr['atr_percent'] . '% (' . \$atr['volatility_category'] . ')' . PHP_EOL;
    }
} else {
    echo '✗ Analysis failed' . PHP_EOL;
}
"

echo ""
echo "======================================"
echo "Tests complete!"
echo "======================================"
