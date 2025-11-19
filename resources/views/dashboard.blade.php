<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Analysis Dashboard - Stockapalooza</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: #0f172a;
            color: #e2e8f0;
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1800px;
            margin: 0 auto;
        }

        header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }

        header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }

        header p {
            opacity: 0.9;
            font-size: 1.1rem;
        }

        .search-bar {
            background: #1e293b;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }

        .search-section {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
            flex-wrap: wrap;
        }

        .search-section input {
            flex: 1;
            min-width: 200px;
            padding: 14px 20px;
            background: #0f172a;
            border: 2px solid #334155;
            border-radius: 8px;
            font-size: 1rem;
            color: #e2e8f0;
            transition: all 0.3s;
        }

        .search-section input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        button {
            padding: 14px 28px;
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

        button.btn-success {
            background: #10b981;
        }

        button.btn-success:hover {
            background: #059669;
        }

        button.btn-danger {
            background: #ef4444;
        }

        button.btn-danger:hover {
            background: #dc2626;
        }

        button.btn-secondary {
            background: #6b7280;
        }

        button.btn-secondary:hover {
            background: #4b5563;
        }

        .quick-picks {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .quick-pick-btn {
            padding: 8px 16px;
            background: #1e293b;
            color: #e2e8f0;
            border: 2px solid #334155;
            border-radius: 6px;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.3s;
        }

        .quick-pick-btn:hover {
            background: #667eea;
            border-color: #667eea;
            color: white;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .card {
            background: #1e293b;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }

        .card h3 {
            color: #667eea;
            font-size: 1.3rem;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #334155;
        }

        .stock-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
        }

        .stock-name h2 {
            font-size: 1.8rem;
            margin-bottom: 5px;
        }

        .stock-symbol {
            color: #667eea;
            font-weight: 600;
        }

        .price-box {
            text-align: right;
        }

        .current-price {
            font-size: 2.5rem;
            font-weight: bold;
        }

        .price-change {
            font-size: 1.2rem;
            font-weight: 600;
            margin-top: 5px;
        }

        .price-change.positive { color: #10b981; }
        .price-change.negative { color: #ef4444; }

        .badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.9rem;
            margin: 5px 5px 5px 0;
        }

        .badge.success { background: #10b981; color: white; }
        .badge.danger { background: #ef4444; color: white; }
        .badge.warning { background: #f59e0b; color: white; }
        .badge.info { background: #3b82f6; color: white; }
        .badge.secondary { background: #6b7280; color: white; }

        .metric-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin: 15px 0;
        }

        .metric {
            background: #0f172a;
            padding: 15px;
            border-radius: 8px;
        }

        .metric-label {
            font-size: 0.85rem;
            color: #94a3b8;
            margin-bottom: 5px;
        }

        .metric-value {
            font-size: 1.5rem;
            font-weight: bold;
            color: #e2e8f0;
        }

        .entry-exit-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin: 15px 0;
        }

        .entry-exit-zone {
            background: #0f172a;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #667eea;
        }

        .zone-label {
            font-size: 0.85rem;
            color: #94a3b8;
            margin-bottom: 8px;
            text-transform: uppercase;
        }

        .zone-price {
            font-size: 1.6rem;
            font-weight: bold;
            color: #e2e8f0;
            margin-bottom: 5px;
        }

        .zone-desc {
            font-size: 0.85rem;
            color: #cbd5e1;
        }

        .zone-distance {
            font-size: 0.85rem;
            margin-top: 8px;
            padding: 5px 10px;
            background: #1e293b;
            border-radius: 4px;
            display: inline-block;
        }

        .loading {
            text-align: center;
            padding: 60px 20px;
            color: #667eea;
        }

        .spinner {
            border: 4px solid #334155;
            border-top: 4px solid #667eea;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .error-box {
            background: #7f1d1d;
            border: 2px solid #dc2626;
            color: #fca5a5;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }

        .support-resistance {
            margin: 15px 0;
        }

        .level-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .level-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #0f172a;
            padding: 12px;
            border-radius: 6px;
        }

        .level-item.support {
            border-left: 4px solid #10b981;
        }

        .level-item.resistance {
            border-left: 4px solid #ef4444;
        }

        .reason-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .reason-item {
            display: flex;
            gap: 12px;
            padding: 12px;
            background: #0f172a;
            border-radius: 6px;
        }

        .reason-icon {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            flex-shrink: 0;
        }

        .reason-icon.positive { background: #10b981; }
        .reason-icon.negative { background: #ef4444; }
        .reason-icon.warning { background: #f59e0b; }
        .reason-icon.neutral { background: #6b7280; }

        .favorite-btn {
            padding: 10px 20px;
            font-size: 1.2rem;
            cursor: pointer;
        }

        .favorites-sidebar {
            position: fixed;
            right: 0;
            top: 0;
            width: 300px;
            height: 100vh;
            background: #1e293b;
            box-shadow: -5px 0 15px rgba(0,0,0,0.3);
            padding: 20px;
            overflow-y: auto;
            transform: translateX(100%);
            transition: transform 0.3s;
            z-index: 1000;
        }

        .favorites-sidebar.open {
            transform: translateX(0);
        }

        .favorites-item {
            background: #0f172a;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .favorites-item:hover {
            background: #334155;
        }

        .toggle-favorites {
            position: fixed;
            right: 20px;
            top: 20px;
            z-index: 999;
            padding: 12px 20px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
        }

        @media (max-width: 768px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }

            .stock-header {
                flex-direction: column;
                gap: 15px;
            }

            .price-box {
                text-align: left;
            }

            .favorites-sidebar {
                width: 100%;
            }
        }

        .swing-indicators {
            margin: 15px 0;
        }

        .indicator-item {
            background: #0f172a;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 10px;
        }

        .indicator-label {
            font-size: 0.9rem;
            color: #94a3b8;
            margin-bottom: 5px;
        }

        .indicator-value {
            font-size: 1.2rem;
            font-weight: 600;
        }

        .phase-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
            border-radius: 12px;
            margin: 20px 0;
            text-align: center;
        }

        .phase-title {
            font-size: 1.8rem;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .phase-desc {
            font-size: 1rem;
            opacity: 0.95;
        }

        .executive-summary {
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            border-radius: 16px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.4);
            border: 2px solid #475569;
        }

        .executive-title {
            font-size: 1.8rem;
            font-weight: bold;
            margin-bottom: 25px;
            text-align: center;
            color: #667eea;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .executive-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }

        .executive-box {
            background: #0f172a;
            padding: 20px;
            border-radius: 12px;
            border-left: 4px solid #667eea;
            transition: transform 0.3s;
        }

        .executive-box:hover {
            transform: translateY(-3px);
        }

        .executive-box.highlight {
            border-left-color: #10b981;
            background: linear-gradient(135deg, #064e3b 0%, #0f172a 100%);
        }

        .executive-box.warning {
            border-left-color: #f59e0b;
        }

        .executive-box.danger {
            border-left-color: #ef4444;
            background: linear-gradient(135deg, #7f1d1d 0%, #0f172a 100%);
        }

        .executive-label {
            font-size: 0.85rem;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }

        .executive-value {
            font-size: 1.8rem;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .executive-desc {
            font-size: 0.9rem;
            color: #cbd5e1;
            line-height: 1.4;
        }

        .quick-actions {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            justify-content: center;
            margin-top: 25px;
            padding-top: 25px;
            border-top: 2px solid #334155;
        }

        .action-button {
            padding: 15px 30px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s;
            border: none;
        }

        .action-button.primary {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }

        .action-button.primary:hover {
            box-shadow: 0 8px 20px rgba(16, 185, 129, 0.4);
            transform: translateY(-2px);
        }

        .action-button.secondary {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
        }

        .action-button.secondary:hover {
            box-shadow: 0 8px 20px rgba(59, 130, 246, 0.4);
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <button class="toggle-favorites" onclick="toggleFavorites()">
        ⭐ Favorites (<span id="favCount">0</span>)
    </button>

    <div class="favorites-sidebar" id="favoritesSidebar">
        <h3 style="margin-bottom: 20px;">My Favorites</h3>
        <div id="favoritesList"></div>
    </div>

    <div class="container">
        <header>
            <h1>📊 Stock Analysis Dashboard</h1>
            <p>Comprehensive trading analysis with Entry/Exit recommendations, Accumulation detection & Swing analysis</p>
        </header>

        <div class="search-bar">
            <div class="search-section">
                <input
                    type="text"
                    id="stockSymbol"
                    placeholder="Enter stock symbol (e.g., BBCA, BBRI, TLKM)"
                    onkeypress="if(event.key==='Enter') loadDashboard()"
                >
                <button onclick="loadDashboard()">Analyze</button>
                <button class="btn-secondary" onclick="window.location.href='/'">Simple View</button>
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

        <div id="dashboard"></div>
    </div>

    <script>
        let currentSymbol = '';
        let isFavorite = false;

        async function loadDashboard() {
            const symbol = document.getElementById('stockSymbol').value.trim().toUpperCase();

            if (!symbol) {
                alert('Please enter a stock symbol');
                return;
            }

            currentSymbol = symbol;
            showLoading();

            try {
                const response = await fetch(`/api/dashboard/${symbol}`);
                const data = await response.json();

                if (data.success) {
                    displayDashboard(data.data);
                } else {
                    showError(data.message);
                }
            } catch (error) {
                showError('Error fetching dashboard data: ' + error.message);
            }
        }

        function quickAnalyze(symbol) {
            document.getElementById('stockSymbol').value = symbol;
            loadDashboard();
        }

        function showLoading() {
            document.getElementById('dashboard').innerHTML = `
                <div class="card loading">
                    <div class="spinner"></div>
                    <p>Loading comprehensive dashboard...</p>
                </div>
            `;
        }

        function showError(message) {
            document.getElementById('dashboard').innerHTML = `
                <div class="error-box">
                    <h3>⚠️ Error</h3>
                    <p>${message}</p>
                </div>
            `;
        }

        function displayDashboard(data) {
            const {stock_info, overall_analysis, entry_exit, swing_analysis, accumulation, is_favorite} = data;
            isFavorite = is_favorite;

            const priceChangeClass = stock_info.change_percent >= 0 ? 'positive' : 'negative';
            const priceChangeSign = stock_info.change_percent >= 0 ? '+' : '';

            const favoriteBtn = isFavorite
                ? `<button class="favorite-btn btn-danger" onclick="removeFavorite()">⭐ Remove from Favorites</button>`
                : `<button class="favorite-btn btn-success" onclick="addFavorite()">☆ Add to Favorites</button>`;

            // Get key values for executive summary
            const bestEntry = entry_exit.entry_recommendation.conservative || entry_exit.entry_recommendation.moderate;
            const bestExit = entry_exit.exit_recommendation.target_2 || entry_exit.exit_recommendation.target_1;
            const overallRec = overall_analysis.recommendation;
            const accPhase = accumulation.phase;
            const swingRating = swing_analysis.swing_rating;

            // Determine executive box classes
            const getExecutiveBoxClass = (action) => {
                if (action.includes('STRONG BUY') || action.includes('BUY')) return 'highlight';
                if (action.includes('SELL')) return 'danger';
                return 'warning';
            };

            let html = `
                <!-- EXECUTIVE SUMMARY -->
                <div class="executive-summary">
                    <div class="executive-title">⚡ Executive Summary</div>

                    <div class="executive-grid">
                        <!-- Overall Recommendation -->
                        <div class="executive-box ${getExecutiveBoxClass(overallRec.action)}">
                            <div class="executive-label">Overall Recommendation</div>
                            <div class="executive-value" style="color: ${overallRec.color === 'success' ? '#10b981' : overallRec.color === 'danger' ? '#ef4444' : '#f59e0b'}">
                                ${overallRec.action}
                            </div>
                            <div class="executive-desc">${overallRec.description}</div>
                        </div>

                        <!-- Score -->
                        <div class="executive-box">
                            <div class="executive-label">Overall Score</div>
                            <div class="executive-value" style="color: #667eea">
                                ${overall_analysis.score.toFixed(0)}/100
                            </div>
                            <div class="executive-desc">
                                Confidence: ${overallRec.confidence}
                                <br>
                                ${overall_analysis.score >= 80 ? 'Excellent opportunity' : overall_analysis.score >= 65 ? 'Good opportunity' : overall_analysis.score >= 50 ? 'Mixed signals' : 'Concerning factors detected'}
                            </div>
                        </div>

                        <!-- Accumulation Phase -->
                        <div class="executive-box ${accPhase.current_phase === 'ACCUMULATION' ? 'highlight' : accPhase.current_phase === 'DISTRIBUTION' ? 'danger' : ''}">
                            <div class="executive-label">Market Phase</div>
                            <div class="executive-value" style="color: ${accPhase.current_phase === 'ACCUMULATION' ? '#10b981' : accPhase.current_phase === 'DISTRIBUTION' ? '#ef4444' : '#f59e0b'}">
                                ${accPhase.current_phase}
                            </div>
                            <div class="executive-desc">${accPhase.description.substring(0, 80)}...</div>
                        </div>

                        <!-- Best Entry Price -->
                        <div class="executive-box">
                            <div class="executive-label">💰 Recommended Entry</div>
                            <div class="executive-value" style="color: #10b981">
                                Rp ${bestEntry.price.toLocaleString()}
                            </div>
                            <div class="executive-desc">
                                ${bestEntry.description}
                                <br>
                                <strong>${bestEntry.distance_percent > 0 ? '↑' : '↓'} ${Math.abs(bestEntry.distance_percent).toFixed(2)}%</strong> from current
                            </div>
                        </div>

                        <!-- Best Exit Target -->
                        <div class="executive-box">
                            <div class="executive-label">🎯 Primary Target</div>
                            <div class="executive-value" style="color: #3b82f6">
                                Rp ${bestExit.price.toLocaleString()}
                            </div>
                            <div class="executive-desc">
                                ${bestExit.description}
                                <br>
                                <strong style="color: #10b981">+${bestExit.potential_gain_percent.toFixed(2)}%</strong> potential gain
                            </div>
                        </div>

                        <!-- Swing Trading Rating -->
                        <div class="executive-box">
                            <div class="executive-label">📈 Swing Rating</div>
                            <div class="executive-value" style="color: #f59e0b">
                                ${swingRating.score}/100
                            </div>
                            <div class="executive-desc">
                                ${swingRating.rating}
                                <br>
                                Avg Swing: ${swing_analysis.swing_size.average_swing_percent.toFixed(1)}%
                            </div>
                        </div>
                    </div>

                    <!-- Quick Action Summary -->
                    <div class="quick-actions">
                        <div style="flex: 1; min-width: 250px; text-align: center;">
                            <div style="font-size: 0.9rem; color: #94a3b8; margin-bottom: 8px; text-transform: uppercase;">Quick Action</div>
                            <div style="font-size: 1.3rem; font-weight: bold; color: #e2e8f0;">
                                ${accumulation.recommendation.action}
                            </div>
                        </div>
                        <div style="flex: 1; min-width: 250px; text-align: center;">
                            <div style="font-size: 0.9rem; color: #94a3b8; margin-bottom: 8px; text-transform: uppercase;">Entry Action</div>
                            <div style="font-size: 1.3rem; font-weight: bold; color: #e2e8f0;">
                                ${entry_exit.position_recommendation.recommended_action}
                            </div>
                        </div>
                        <div style="flex: 1; min-width: 250px; text-align: center;">
                            <div style="font-size: 0.9rem; color: #94a3b8; margin-bottom: 8px; text-transform: uppercase;">Swing Pattern</div>
                            <div style="font-size: 1.3rem; font-weight: bold; color: #e2e8f0;">
                                ${swing_analysis.swing_pattern.pattern}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stock Header -->
                <div class="card">
                    <div class="stock-header">
                        <div class="stock-name">
                            <h2>${stock_info.name}</h2>
                            <p class="stock-symbol">${stock_info.symbol}</p>
                        </div>
                        <div class="price-box">
                            <div class="current-price">Rp ${stock_info.current_price.toLocaleString()}</div>
                            <div class="price-change ${priceChangeClass}">
                                ${priceChangeSign}${stock_info.change_percent.toFixed(2)}%
                            </div>
                        </div>
                    </div>
                    <div style="text-align: center; margin: 20px 0;">
                        ${favoriteBtn}
                    </div>
                </div>

                <!-- Accumulation Phase -->
                <div class="card">
                    <div class="phase-box" style="background: ${getPhaseColor(accumulation.phase.current_phase)}">
                        <div class="phase-title">${accumulation.phase.current_phase}</div>
                        <div class="phase-desc">${accumulation.phase.description}</div>
                        <div style="margin-top: 15px;">
                            <span class="badge secondary">Confidence: ${accumulation.phase.confidence}</span>
                        </div>
                    </div>
                    <h3>📊 Accumulation Analysis</h3>
                    <div class="metric-row">
                        <div class="metric">
                            <div class="metric-label">Accumulation Strength</div>
                            <div class="metric-value">${accumulation.strength.score}/100</div>
                            <div style="margin-top: 5px; font-size: 0.9rem;">${accumulation.strength.strength}</div>
                        </div>
                        <div class="metric">
                            <div class="metric-label">OBV Trend</div>
                            <div class="metric-value">${accumulation.obv_analysis.trend}</div>
                            <div style="margin-top: 5px; font-size: 0.85rem;">${accumulation.obv_analysis.interpretation}</div>
                        </div>
                        <div class="metric">
                            <div class="metric-label">Volume Status</div>
                            <div class="metric-value">${accumulation.current_volume_vs_average.ratio}x</div>
                            <div style="margin-top: 5px; font-size: 0.85rem;">${accumulation.current_volume_vs_average.status}</div>
                        </div>
                    </div>
                    <div style="margin-top: 20px;">
                        <h4 style="margin-bottom: 10px;">Recommendation</h4>
                        <div class="badge ${getActionBadgeClass(accumulation.recommendation.action)}" style="font-size: 1.1rem; padding: 12px 20px;">
                            ${accumulation.recommendation.action}
                        </div>
                        <div class="reason-list" style="margin-top: 15px;">
                            ${accumulation.recommendation.reasoning.map(r => `
                                <div class="reason-item">
                                    <div class="reason-icon info">ℹ</div>
                                    <div>${r}</div>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                </div>

                <div class="dashboard-grid">
                    <!-- Entry/Exit Analysis -->
                    <div class="card">
                        <h3>🎯 Entry Price Recommendations</h3>
                        <div class="entry-exit-grid">
                            ${Object.entries(entry_exit.entry_recommendation).map(([type, zone]) => `
                                <div class="entry-exit-zone">
                                    <div class="zone-label">${type}</div>
                                    <div class="zone-price">Rp ${zone.price.toLocaleString()}</div>
                                    <div class="zone-desc">${zone.description}</div>
                                    <div class="zone-distance ${zone.distance_percent > 0 ? 'positive' : 'negative'}">
                                        ${zone.distance_percent > 0 ? '↑' : '↓'} ${Math.abs(zone.distance_percent).toFixed(2)}% from current
                                    </div>
                                </div>
                            `).join('')}
                        </div>

                        <h3 style="margin-top: 30px;">🚀 Exit Price Targets</h3>
                        <div class="entry-exit-grid">
                            ${Object.entries(entry_exit.exit_recommendation).map(([type, zone]) => `
                                <div class="entry-exit-zone" style="border-left-color: #10b981;">
                                    <div class="zone-label">${type}</div>
                                    <div class="zone-price">Rp ${zone.price.toLocaleString()}</div>
                                    <div class="zone-desc">${zone.description}</div>
                                    <div class="zone-distance positive">
                                        ↑ ${zone.potential_gain_percent.toFixed(2)}% potential gain
                                    </div>
                                </div>
                            `).join('')}
                        </div>

                        <div style="margin-top: 25px; padding: 15px; background: #0f172a; border-radius: 8px;">
                            <h4 style="margin-bottom: 10px;">Current Position</h4>
                            <div class="badge ${getActionBadgeClass(entry_exit.position_recommendation.recommended_action)}" style="font-size: 1rem;">
                                ${entry_exit.position_recommendation.recommended_action}
                            </div>
                            <p style="margin-top: 10px; color: #cbd5e1; font-size: 0.95rem;">
                                ${entry_exit.position_recommendation.description}
                            </p>
                        </div>
                    </div>

                    <!-- Swing Analysis -->
                    <div class="card">
                        <h3>📈 Swing Trading Analysis</h3>
                        <div class="metric-row">
                            <div class="metric">
                                <div class="metric-label">Avg Swing Size</div>
                                <div class="metric-value">${swing_analysis.swing_size.average_swing_percent.toFixed(2)}%</div>
                                <div style="margin-top: 5px; font-size: 0.85rem;">${swing_analysis.swing_size.swing_size_category}</div>
                            </div>
                            <div class="metric">
                                <div class="metric-label">Volatility</div>
                                <div class="metric-value">${swing_analysis.volatility.daily_volatility_percent.toFixed(2)}%</div>
                                <div style="margin-top: 5px; font-size: 0.85rem;">${swing_analysis.volatility.volatility_rating}</div>
                            </div>
                        </div>

                        <div style="margin: 20px 0;">
                            <h4 style="margin-bottom: 10px;">Swing Pattern</h4>
                            <div class="badge info" style="font-size: 1rem; padding: 12px 18px;">
                                ${swing_analysis.swing_pattern.pattern}
                            </div>
                            <p style="margin-top: 10px; color: #cbd5e1; font-size: 0.9rem;">
                                ${swing_analysis.swing_pattern.description}
                            </p>
                        </div>

                        <div style="margin-top: 20px;">
                            <h4 style="margin-bottom: 10px;">Bollinger Bands</h4>
                            <div class="metric-row">
                                <div class="metric">
                                    <div class="metric-label">Upper Band</div>
                                    <div class="metric-value" style="font-size: 1.2rem;">${swing_analysis.bollinger_bands.upper_band}</div>
                                </div>
                                <div class="metric">
                                    <div class="metric-label">Middle (SMA)</div>
                                    <div class="metric-value" style="font-size: 1.2rem;">${swing_analysis.bollinger_bands.middle_band}</div>
                                </div>
                                <div class="metric">
                                    <div class="metric-label">Lower Band</div>
                                    <div class="metric-value" style="font-size: 1.2rem;">${swing_analysis.bollinger_bands.lower_band}</div>
                                </div>
                            </div>
                            <div style="margin-top: 10px; padding: 12px; background: #0f172a; border-radius: 6px;">
                                <strong>Band Status:</strong> ${swing_analysis.bollinger_bands.squeeze_status}
                                <br>
                                <strong>Price Position:</strong> ${swing_analysis.bollinger_bands.price_position_percent.toFixed(1)}% within bands
                            </div>
                        </div>

                        <div style="margin-top: 25px;">
                            <h4 style="margin-bottom: 10px;">Swing Signals</h4>
                            <div class="reason-list">
                                ${swing_analysis.swing_signals.map(signal => `
                                    <div class="reason-item">
                                        <div class="reason-icon ${getSignalClass(signal.type)}">${getSignalIcon(signal.type)}</div>
                                        <div>
                                            <strong>${signal.signal}</strong> (${signal.strength})
                                            <br>
                                            <span style="font-size: 0.9rem; color: #94a3b8;">${signal.description}</span>
                                        </div>
                                    </div>
                                `).join('')}
                            </div>
                        </div>

                        <div style="margin-top: 25px; padding: 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px; text-align: center;">
                            <h4 style="margin-bottom: 10px;">Swing Trading Rating</h4>
                            <div style="font-size: 2rem; font-weight: bold; margin: 10px 0;">
                                ${swing_analysis.swing_rating.score}/100
                            </div>
                            <div style="font-size: 1.2rem; font-weight: 600;">
                                ${swing_analysis.swing_rating.rating}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Support & Resistance Levels -->
                <div class="card">
                    <h3>📍 Support & Resistance Levels</h3>
                    <div class="dashboard-grid" style="margin-top: 20px;">
                        <div>
                            <h4 style="margin-bottom: 15px; color: #10b981;">Support Levels (Buy Zones)</h4>
                            <div class="level-list">
                                ${entry_exit.support_levels.length > 0
                                    ? entry_exit.support_levels.map((level, i) => `
                                        <div class="level-item support">
                                            <span>Support ${i + 1}</span>
                                            <strong>Rp ${level.toLocaleString()}</strong>
                                        </div>
                                    `).join('')
                                    : '<p style="color: #94a3b8;">No clear support levels detected</p>'
                                }
                            </div>
                        </div>
                        <div>
                            <h4 style="margin-bottom: 15px; color: #ef4444;">Resistance Levels (Sell Zones)</h4>
                            <div class="level-list">
                                ${entry_exit.resistance_levels.length > 0
                                    ? entry_exit.resistance_levels.map((level, i) => `
                                        <div class="level-item resistance">
                                            <span>Resistance ${i + 1}</span>
                                            <strong>Rp ${level.toLocaleString()}</strong>
                                        </div>
                                    `).join('')
                                    : '<p style="color: #94a3b8;">No clear resistance levels detected</p>'
                                }
                            </div>
                        </div>
                    </div>

                    <h3 style="margin-top: 30px;">🔢 Fibonacci Retracement Levels</h3>
                    <div class="metric-row">
                        ${Object.entries(entry_exit.fibonacci_levels).map(([level, price]) => `
                            <div class="metric">
                                <div class="metric-label">${level.replace('level_', '').replace('_', '.')}%</div>
                                <div class="metric-value" style="font-size: 1.2rem;">Rp ${price.toLocaleString()}</div>
                            </div>
                        `).join('')}
                    </div>
                </div>

                <!-- Overall Analysis Summary -->
                <div class="card">
                    <h3>📋 Overall Analysis Summary</h3>
                    <div style="text-align: center; margin: 25px 0;">
                        <div style="font-size: 3rem; font-weight: bold; color: #667eea; margin-bottom: 10px;">
                            ${overall_analysis.score.toFixed(1)}/100
                        </div>
                        <div class="badge ${overall_analysis.recommendation.color}" style="font-size: 1.3rem; padding: 15px 30px;">
                            ${overall_analysis.recommendation.action}
                        </div>
                        <p style="margin-top: 15px; font-size: 1.1rem; color: #cbd5e1;">
                            ${overall_analysis.recommendation.description}
                        </p>
                    </div>

                    <div class="metric-row">
                        ${Object.entries(overall_analysis.analysis).map(([category, data]) => `
                            <div class="metric">
                                <div class="metric-label">${category}</div>
                                <div class="metric-value" style="font-size: 1.3rem;">${data.score.toFixed(1)}%</div>
                                <div style="margin-top: 8px; height: 8px; background: #0f172a; border-radius: 4px; overflow: hidden;">
                                    <div style="width: ${data.score}%; height: 100%; background: linear-gradient(90deg, #667eea, #764ba2);"></div>
                                </div>
                            </div>
                        `).join('')}
                    </div>

                    <h4 style="margin: 30px 0 15px 0;">Key Insights</h4>
                    <div class="reason-list">
                        ${overall_analysis.reasons.slice(0, 10).map(reason => `
                            <div class="reason-item">
                                <div class="reason-icon ${reason.type}">${getReasonIcon(reason.type)}</div>
                                <div>${reason.message}</div>
                            </div>
                        `).join('')}
                    </div>
                </div>
            `;

            document.getElementById('dashboard').innerHTML = html;
        }

        function getPhaseColor(phase) {
            const colors = {
                'ACCUMULATION': 'linear-gradient(135deg, #10b981 0%, #059669 100%)',
                'MARKUP': 'linear-gradient(135deg, #3b82f6 0%, #2563eb 100%)',
                'DISTRIBUTION': 'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)',
                'MARKDOWN': 'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)',
                'NEUTRAL/CONSOLIDATION': 'linear-gradient(135deg, #6b7280 0%, #4b5563 100%)'
            };
            return colors[phase] || 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)';
        }

        function getActionBadgeClass(action) {
            if (action.includes('BUY')) return 'success';
            if (action.includes('SELL')) return 'danger';
            if (action.includes('WAIT') || action.includes('HOLD')) return 'warning';
            return 'secondary';
        }

        function getSignalClass(type) {
            const classes = {
                'BUY': 'positive',
                'SELL': 'negative',
                'ALERT': 'warning',
                'HOLD': 'neutral'
            };
            return classes[type] || 'neutral';
        }

        function getSignalIcon(type) {
            const icons = {
                'BUY': '↑',
                'SELL': '↓',
                'ALERT': '⚠',
                'HOLD': '='
            };
            return icons[type] || 'i';
        }

        function getReasonIcon(type) {
            const icons = {
                'positive': '✓',
                'negative': '✗',
                'warning': '!',
                'neutral': 'i'
            };
            return icons[type] || 'i';
        }

        async function addFavorite() {
            try {
                const symbol = currentSymbol;
                const name = document.querySelector('.stock-name h2').textContent;

                const response = await fetch('/api/favorites', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({symbol, name})
                });

                const data = await response.json();
                if (data.success) {
                    alert('Added to favorites!');
                    loadDashboard();
                    loadFavorites();
                } else {
                    alert(data.message);
                }
            } catch (error) {
                alert('Error adding to favorites: ' + error.message);
            }
        }

        async function removeFavorite() {
            try {
                const response = await fetch(`/api/favorites/${currentSymbol}`, {
                    method: 'DELETE'
                });

                const data = await response.json();
                if (data.success) {
                    alert('Removed from favorites!');
                    loadDashboard();
                    loadFavorites();
                }
            } catch (error) {
                alert('Error removing from favorites: ' + error.message);
            }
        }

        async function loadFavorites() {
            try {
                const response = await fetch('/api/favorites');
                const data = await response.json();

                if (data.success) {
                    document.getElementById('favCount').textContent = data.count;

                    if (data.count === 0) {
                        document.getElementById('favoritesList').innerHTML = '<p style="color: #94a3b8;">No favorites yet</p>';
                    } else {
                        const html = data.data.map(fav => `
                            <div class="favorites-item" onclick="loadFavoriteStock('${fav.symbol.replace('.JK', '')}')">
                                <strong>${fav.symbol}</strong>
                                <div style="font-size: 0.85rem; color: #94a3b8; margin-top: 5px;">${fav.name}</div>
                            </div>
                        `).join('');
                        document.getElementById('favoritesList').innerHTML = html;
                    }
                }
            } catch (error) {
                console.error('Error loading favorites:', error);
            }
        }

        function loadFavoriteStock(symbol) {
            document.getElementById('stockSymbol').value = symbol;
            loadDashboard();
            toggleFavorites();
        }

        function toggleFavorites() {
            const sidebar = document.getElementById('favoritesSidebar');
            sidebar.classList.toggle('open');
        }

        // Load favorites on page load
        window.addEventListener('load', () => {
            loadFavorites();
            // Auto-load BBCA as example
            document.getElementById('stockSymbol').value = 'BBCA';
            loadDashboard();
        });
    </script>
</body>
</html>
