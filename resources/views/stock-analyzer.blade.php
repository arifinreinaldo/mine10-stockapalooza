<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stockapalooza - Indonesian Stock Analyzer</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        header {
            text-align: center;
            color: white;
            margin-bottom: 30px;
        }

        header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }

        header p {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        .search-section {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .search-section input {
            flex: 1;
            min-width: 200px;
            padding: 12px 20px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        .search-section input:focus {
            outline: none;
            border-color: #667eea;
        }

        button {
            padding: 12px 30px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        button:hover {
            background: #5568d3;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        button:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
        }

        .quick-picks {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 15px;
        }

        .quick-pick-btn {
            padding: 8px 16px;
            background: #f0f0f0;
            color: #333;
            border: 2px solid #e0e0e0;
            border-radius: 6px;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.3s;
        }

        .quick-pick-btn:hover {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }

        .loading {
            text-align: center;
            padding: 40px;
            color: #667eea;
        }

        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #667eea;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .analysis-result {
            margin-top: 20px;
        }

        .stock-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }

        .stock-info h2 {
            font-size: 1.8rem;
            color: #333;
            margin-bottom: 5px;
        }

        .stock-info .symbol {
            color: #667eea;
            font-weight: 600;
        }

        .price-info {
            text-align: right;
        }

        .current-price {
            font-size: 2rem;
            font-weight: bold;
            color: #333;
        }

        .price-change {
            font-size: 1.1rem;
            font-weight: 600;
            margin-top: 5px;
        }

        .price-change.positive { color: #10b981; }
        .price-change.negative { color: #ef4444; }

        .recommendation-badge {
            display: inline-block;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: bold;
            font-size: 1.2rem;
            margin: 20px 0;
        }

        .recommendation-badge.success {
            background: #10b981;
            color: white;
        }

        .recommendation-badge.warning {
            background: #f59e0b;
            color: white;
        }

        .recommendation-badge.danger {
            background: #ef4444;
            color: white;
        }

        .score-section {
            display: flex;
            align-items: center;
            gap: 20px;
            margin: 20px 0;
            padding: 20px;
            background: #f9fafb;
            border-radius: 8px;
        }

        .score-circle {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            font-weight: bold;
            color: white;
            background: conic-gradient(#10b981 0deg, #10b981 calc(var(--score) * 3.6deg), #e5e7eb calc(var(--score) * 3.6deg), #e5e7eb 360deg);
        }

        .score-details {
            flex: 1;
        }

        .analysis-categories {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }

        .category-card {
            padding: 15px;
            background: #f9fafb;
            border-radius: 8px;
            border-left: 4px solid #667eea;
        }

        .category-card h4 {
            color: #333;
            margin-bottom: 10px;
            font-size: 1rem;
        }

        .category-score {
            font-size: 1.5rem;
            font-weight: bold;
            color: #667eea;
        }

        .progress-bar {
            width: 100%;
            height: 8px;
            background: #e5e7eb;
            border-radius: 4px;
            overflow: hidden;
            margin-top: 8px;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #667eea, #764ba2);
            transition: width 0.5s ease;
        }

        .reasons-section {
            margin-top: 30px;
        }

        .reasons-section h3 {
            color: #333;
            margin-bottom: 15px;
            font-size: 1.3rem;
        }

        .reason-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 12px;
            margin-bottom: 10px;
            border-radius: 6px;
            background: #f9fafb;
        }

        .reason-icon {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            flex-shrink: 0;
        }

        .reason-icon.positive {
            background: #10b981;
            color: white;
        }

        .reason-icon.negative {
            background: #ef4444;
            color: white;
        }

        .reason-icon.warning {
            background: #f59e0b;
            color: white;
        }

        .reason-icon.neutral {
            background: #6b7280;
            color: white;
        }

        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }

        .metric-item {
            padding: 15px;
            background: #f9fafb;
            border-radius: 8px;
        }

        .metric-label {
            font-size: 0.85rem;
            color: #6b7280;
            margin-bottom: 5px;
        }

        .metric-value {
            font-size: 1.3rem;
            font-weight: bold;
            color: #333;
        }

        .error-message {
            padding: 15px;
            background: #fee2e2;
            color: #dc2626;
            border-radius: 8px;
            margin: 20px 0;
        }

        .multiple-results {
            display: grid;
            gap: 20px;
        }

        .stock-card-mini {
            padding: 20px;
            background: white;
            border-radius: 8px;
            border-left: 4px solid #667eea;
            cursor: pointer;
            transition: all 0.3s;
        }

        .stock-card-mini:hover {
            transform: translateX(5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .stock-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        @media (max-width: 768px) {
            header h1 {
                font-size: 2rem;
            }

            .stock-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            .price-info {
                text-align: left;
            }

            .search-section {
                flex-direction: column;
            }

            .search-section input {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>📈 Stockapalooza</h1>
            <p>Indonesian Stock Market Analysis & Recommendation System</p>
        </header>

        <div class="card">
            <div class="search-section">
                <input
                    type="text"
                    id="stockSymbol"
                    placeholder="Enter stock symbol (e.g., BBCA, BBRI, TLKM)"
                    onkeypress="if(event.key==='Enter') analyzeStock()"
                >
                <button onclick="analyzeStock()" id="analyzeBtn">Analyze Stock</button>
                <button onclick="loadTopStocks()" id="topStocksBtn">Top 10 Stocks</button>
            </div>

            <div class="quick-picks">
                <strong style="margin-right: 10px;">Quick picks:</strong>
                <button class="quick-pick-btn" onclick="quickAnalyze('BBCA')">BBCA</button>
                <button class="quick-pick-btn" onclick="quickAnalyze('BBRI')">BBRI</button>
                <button class="quick-pick-btn" onclick="quickAnalyze('BMRI')">BMRI</button>
                <button class="quick-pick-btn" onclick="quickAnalyze('TLKM')">TLKM</button>
                <button class="quick-pick-btn" onclick="quickAnalyze('ASII')">ASII</button>
                <button class="quick-pick-btn" onclick="quickAnalyze('GOTO')">GOTO</button>
                <button class="quick-pick-btn" onclick="quickAnalyze('UNVR')">UNVR</button>
            </div>
        </div>

        <div id="results"></div>
    </div>

    <script>
        async function analyzeStock() {
            const symbol = document.getElementById('stockSymbol').value.trim().toUpperCase();

            if (!symbol) {
                alert('Please enter a stock symbol');
                return;
            }

            showLoading();

            try {
                const response = await fetch(`/api/analyze/${symbol}`);
                const data = await response.json();

                if (data.success) {
                    displayAnalysis(data.data);
                } else {
                    showError(data.message);
                }
            } catch (error) {
                showError('Error fetching stock data: ' + error.message);
            }
        }

        async function loadTopStocks() {
            showLoading();

            try {
                const response = await fetch('/api/top-stocks');
                const data = await response.json();

                if (data.success) {
                    displayMultipleAnalyses(data.data);
                } else {
                    showError(data.message);
                }
            } catch (error) {
                showError('Error fetching top stocks: ' + error.message);
            }
        }

        function quickAnalyze(symbol) {
            document.getElementById('stockSymbol').value = symbol;
            analyzeStock();
        }

        function showLoading() {
            document.getElementById('results').innerHTML = `
                <div class="card loading">
                    <div class="spinner"></div>
                    <p>Analyzing stock data...</p>
                </div>
            `;
        }

        function showError(message) {
            document.getElementById('results').innerHTML = `
                <div class="card">
                    <div class="error-message">
                        ⚠️ ${message}
                    </div>
                </div>
            `;
        }

        function displayAnalysis(analysis) {
            const priceChange = analysis.metrics.price.change_percent;
            const priceChangeClass = priceChange >= 0 ? 'positive' : 'negative';
            const priceChangeSign = priceChange >= 0 ? '+' : '';

            let categoriesHTML = '';
            for (const [category, data] of Object.entries(analysis.analysis)) {
                categoriesHTML += `
                    <div class="category-card">
                        <h4>${category}</h4>
                        <div class="category-score">${data.score.toFixed(1)}%</div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: ${data.score}%"></div>
                        </div>
                        <small>${data.points} / ${data.max_points} points</small>
                    </div>
                `;
            }

            let reasonsHTML = '';
            for (const reason of analysis.reasons) {
                const icon = reason.type === 'positive' ? '✓' :
                            reason.type === 'negative' ? '✗' :
                            reason.type === 'warning' ? '!' : 'i';
                reasonsHTML += `
                    <div class="reason-item">
                        <div class="reason-icon ${reason.type}">${icon}</div>
                        <div>${reason.message}</div>
                    </div>
                `;
            }

            const metricsHTML = `
                <div class="metric-item">
                    <div class="metric-label">P/E Ratio</div>
                    <div class="metric-value">${analysis.metrics.valuation.pe_ratio?.toFixed(2) ?? 'N/A'}</div>
                </div>
                <div class="metric-item">
                    <div class="metric-label">P/B Ratio</div>
                    <div class="metric-value">${analysis.metrics.valuation.pb_ratio?.toFixed(2) ?? 'N/A'}</div>
                </div>
                <div class="metric-item">
                    <div class="metric-label">ROE</div>
                    <div class="metric-value">${analysis.metrics.profitability.roe ? analysis.metrics.profitability.roe.toFixed(2) + '%' : 'N/A'}</div>
                </div>
                <div class="metric-item">
                    <div class="metric-label">Profit Margin</div>
                    <div class="metric-value">${analysis.metrics.profitability.profit_margin ? analysis.metrics.profitability.profit_margin.toFixed(2) + '%' : 'N/A'}</div>
                </div>
                <div class="metric-item">
                    <div class="metric-label">Dividend Yield</div>
                    <div class="metric-value">${analysis.metrics.dividend.yield.toFixed(2)}%</div>
                </div>
                <div class="metric-item">
                    <div class="metric-label">Market Cap</div>
                    <div class="metric-value">Rp ${(analysis.metrics.valuation.market_cap / 1_000_000_000_000).toFixed(2)}T</div>
                </div>
            `;

            document.getElementById('results').innerHTML = `
                <div class="card analysis-result">
                    <div class="stock-header">
                        <div class="stock-info">
                            <h2>${analysis.name}</h2>
                            <p class="symbol">${analysis.symbol}</p>
                        </div>
                        <div class="price-info">
                            <div class="current-price">Rp ${analysis.current_price.toLocaleString()}</div>
                            <div class="price-change ${priceChangeClass}">
                                ${priceChangeSign}${priceChange.toFixed(2)}%
                            </div>
                        </div>
                    </div>

                    <div class="score-section">
                        <div class="score-circle" style="--score: ${analysis.score}">
                            <span>${analysis.score.toFixed(0)}</span>
                        </div>
                        <div class="score-details">
                            <h3>Overall Score: ${analysis.score.toFixed(2)} / 100</h3>
                            <div class="recommendation-badge ${analysis.recommendation.color}">
                                ${analysis.recommendation.action}
                            </div>
                            <p><strong>Confidence:</strong> ${analysis.recommendation.confidence}</p>
                            <p>${analysis.recommendation.description}</p>
                        </div>
                    </div>

                    <h3 style="margin: 30px 0 15px 0; color: #333;">Analysis Breakdown</h3>
                    <div class="analysis-categories">
                        ${categoriesHTML}
                    </div>

                    <h3 style="margin: 30px 0 15px 0; color: #333;">Key Metrics</h3>
                    <div class="metrics-grid">
                        ${metricsHTML}
                    </div>

                    <div class="reasons-section">
                        <h3>Detailed Reasoning</h3>
                        ${reasonsHTML}
                    </div>
                </div>
            `;
        }

        function displayMultipleAnalyses(analyses) {
            let html = '<div class="card"><h2 style="margin-bottom: 20px; color: #333;">Top Indonesian Stocks - Ranked by Score</h2></div>';

            html += '<div class="multiple-results">';

            analyses.forEach((analysis, index) => {
                const priceChange = analysis.metrics.price.change_percent;
                const priceChangeClass = priceChange >= 0 ? 'positive' : 'negative';
                const priceChangeSign = priceChange >= 0 ? '+' : '';

                html += `
                    <div class="card stock-card-mini" onclick='document.getElementById("stockSymbol").value="${analysis.symbol.replace('.JK', '')}"; analyzeStock();'>
                        <div class="stock-card-header">
                            <div>
                                <h3 style="color: #333; margin-bottom: 5px;">#${index + 1} ${analysis.name}</h3>
                                <p style="color: #667eea; font-weight: 600;">${analysis.symbol}</p>
                            </div>
                            <div style="text-align: right;">
                                <div style="font-size: 1.5rem; font-weight: bold; color: #333;">Rp ${analysis.current_price.toLocaleString()}</div>
                                <div class="price-change ${priceChangeClass}" style="font-size: 1rem;">
                                    ${priceChangeSign}${priceChange.toFixed(2)}%
                                </div>
                            </div>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 15px;">
                            <div>
                                <div class="recommendation-badge ${analysis.recommendation.color}" style="font-size: 1rem; padding: 8px 16px;">
                                    ${analysis.recommendation.action}
                                </div>
                            </div>
                            <div style="text-align: right;">
                                <div style="font-size: 2rem; font-weight: bold; color: #667eea;">${analysis.score.toFixed(0)}</div>
                                <div style="font-size: 0.85rem; color: #6b7280;">Score</div>
                            </div>
                        </div>
                        <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #e5e7eb; display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; font-size: 0.85rem;">
                            <div>
                                <div style="color: #6b7280;">P/E Ratio</div>
                                <div style="font-weight: 600;">${analysis.metrics.valuation.pe_ratio?.toFixed(2) ?? 'N/A'}</div>
                            </div>
                            <div>
                                <div style="color: #6b7280;">ROE</div>
                                <div style="font-weight: 600;">${analysis.metrics.profitability.roe ? analysis.metrics.profitability.roe.toFixed(2) + '%' : 'N/A'}</div>
                            </div>
                            <div>
                                <div style="color: #6b7280;">Div Yield</div>
                                <div style="font-weight: 600;">${analysis.metrics.dividend.yield.toFixed(2)}%</div>
                            </div>
                        </div>
                        <p style="margin-top: 15px; color: #6b7280; font-size: 0.9rem;">Click to see detailed analysis →</p>
                    </div>
                `;
            });

            html += '</div>';

            document.getElementById('results').innerHTML = html;
        }

        // Load top stocks on page load
        window.addEventListener('load', () => {
            loadTopStocks();
        });
    </script>
</body>
</html>
