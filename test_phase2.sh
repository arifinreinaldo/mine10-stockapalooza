#!/bin/bash

echo "======================================"
echo "Phase 2 Indicators - Test Suite"
echo "======================================"
echo ""

# Test 1: Check syntax
echo "✓ Test 1: Checking if code compiles..."
php -l app/Data/IndonesianMarketData.php
php -l app/Services/StockAnalyzer.php

echo ""
echo "======================================"
echo "Testing BBRI (BUMN Bank - Sharia)"
echo "======================================"
php artisan tinker --execute="
\$fetcher = app(App\Services\StockDataFetcher::class);
\$analyzer = new App\Services\StockAnalyzer();
\$data = \$fetcher->fetchStockData('BBRI.JK');
if (\$data) {
    \$analysis = \$analyzer->analyze(\$data);
    \$context = \$analysis['metrics']['market_context'] ?? [];

    echo '📊 BBRI Analysis' . PHP_EOL;
    echo '  Score: ' . \$analysis['score'] . '/100' . PHP_EOL;

    if (isset(\$context['liquidity'])) {
        echo PHP_EOL . '💧 Liquidity:' . PHP_EOL;
        echo '  Score: ' . \$context['liquidity']['score'] . '/100' . PHP_EOL;
        echo '  Category: ' . \$context['liquidity']['category'] . PHP_EOL;
    }

    if (isset(\$context['sharia_compliance'])) {
        echo PHP_EOL . '🕌 Sharia Compliance:' . PHP_EOL;
        echo '  Compliant: ' . (\$context['sharia_compliance']['is_compliant'] ? 'YES' : 'NO') . PHP_EOL;
        echo '  In DES List: ' . (\$context['sharia_compliance']['in_des_list'] ? 'YES' : 'NO') . PHP_EOL;
        echo '  Sector: ' . (\$context['sharia_compliance']['sector'] ?? 'Unknown') . PHP_EOL;
    }

    if (isset(\$context['bumn_status'])) {
        echo PHP_EOL . '🏛️ BUMN Status:' . PHP_EOL;
        echo '  Is BUMN: ' . (\$context['bumn_status']['is_bumn'] ? 'YES' : 'NO') . PHP_EOL;
        if (\$context['bumn_status']['is_bumn']) {
            \$info = \$context['bumn_status']['bumn_info'];
            echo '  Tier: ' . \$info['tier'] . PHP_EOL;
            echo '  Sector: ' . \$info['sector'] . PHP_EOL;
            echo '  Gov Ownership: ' . \$info['ownership'] . '%' . PHP_EOL;
        }
    }

    if (isset(\$context['sector_rotation'])) {
        echo PHP_EOL . '🔄 Sector Rotation:' . PHP_EOL;
        echo '  Sector: ' . \$context['sector_rotation']['sector'] . PHP_EOL;
        echo '  Status: ' . \$context['sector_rotation']['sector_status'] . PHP_EOL;
        echo '  1-Month Momentum: ' . \$context['sector_rotation']['momentum_1month_percent'] . '%' . PHP_EOL;
        echo '  Recommendation: ' . \$context['sector_rotation']['recommendation'] . PHP_EOL;
    }
}
"

echo ""
echo "======================================"
echo "Testing GOTO (Tech - Sharia - Private)"
echo "======================================"
php artisan tinker --execute="
\$fetcher = app(App\Services\StockDataFetcher::class);
\$analyzer = new App\Services\StockAnalyzer();
\$data = \$fetcher->fetchStockData('GOTO.JK');
if (\$data) {
    \$analysis = \$analyzer->analyze(\$data);
    \$context = \$analysis['metrics']['market_context'] ?? [];

    echo '📊 GOTO Analysis' . PHP_EOL;
    echo '  Score: ' . \$analysis['score'] . '/100' . PHP_EOL;

    if (isset(\$context['liquidity'])) {
        echo PHP_EOL . '💧 Liquidity:' . PHP_EOL;
        echo '  Category: ' . \$context['liquidity']['category'] . PHP_EOL;
    }

    if (isset(\$context['sharia_compliance'])) {
        echo PHP_EOL . '🕌 Sharia: ' . (\$context['sharia_compliance']['is_compliant'] ? 'YES' : 'NO') . PHP_EOL;
    }

    if (isset(\$context['bumn_status'])) {
        echo '🏛️ BUMN: ' . (\$context['bumn_status']['is_bumn'] ? 'YES' : 'NO') . PHP_EOL;
    }

    if (isset(\$context['sector_rotation'])) {
        echo '🔄 Sector: ' . \$context['sector_rotation']['sector'] . ' (' . \$context['sector_rotation']['sector_status'] . ')' . PHP_EOL;
    }
}
"

echo ""
echo "======================================"
echo "Testing BBCA (Private Bank - NOT Sharia)"
echo "======================================"
php artisan tinker --execute="
\$fetcher = app(App\Services\StockDataFetcher::class);
\$analyzer = new App\Services\StockAnalyzer();
\$data = \$fetcher->fetchStockData('BBCA.JK');
if (\$data) {
    \$analysis = \$analyzer->analyze(\$data);
    \$context = \$analysis['metrics']['market_context'] ?? [];

    echo '📊 BBCA Analysis' . PHP_EOL;
    echo '  Score: ' . \$analysis['score'] . '/100' . PHP_EOL;

    if (isset(\$context['liquidity'])) {
        echo PHP_EOL . '💧 Liquidity: ' . \$context['liquidity']['category'] . PHP_EOL;
    }

    if (isset(\$context['sharia_compliance'])) {
        echo '🕌 Sharia: ' . (\$context['sharia_compliance']['is_compliant'] ? 'YES' : 'NO') . PHP_EOL;
        if (!\$context['sharia_compliance']['is_compliant']) {
            echo '  Reason: ' . implode(', ', \$context['sharia_compliance']['notes']) . PHP_EOL;
        }
    }

    if (isset(\$context['bumn_status'])) {
        echo '🏛️ BUMN: ' . (\$context['bumn_status']['is_bumn'] ? 'YES' : 'NO') . PHP_EOL;
    }

    if (isset(\$context['sector_rotation'])) {
        echo '🔄 Sector: ' . \$context['sector_rotation']['sector'] . ' (' . \$context['sector_rotation']['sector_status'] . ')' . PHP_EOL;
    }
}
"

echo ""
echo "======================================"
echo "Phase 2 Tests Complete!"
echo "======================================"
echo ""
echo "Summary of Phase 2 Features:"
echo "✅ Liquidity Score - Measures ease of trading"
echo "✅ Sharia Compliance - OJK DES list validation"
echo "✅ BUMN Status - Government ownership identification"
echo "✅ Sector Rotation - Sector momentum analysis"
