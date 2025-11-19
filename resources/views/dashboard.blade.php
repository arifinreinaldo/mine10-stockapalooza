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
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .card {
            background: #1e293b;
            border-radius: 10px;
            padding: 18px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }

        .card h3 {
            color: #667eea;
            font-size: 1.1rem;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #334155;
        }

        .card h4 {
            font-size: 1rem;
            margin-bottom: 10px;
        }

        .compact-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 15px;
        }

        @media (max-width: 1200px) {
            .compact-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .compact-grid {
                grid-template-columns: 1fr;
            }
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
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 10px;
            margin: 10px 0;
        }

        .metric {
            background: #0f172a;
            padding: 10px 12px;
            border-radius: 6px;
        }

        .metric-label {
            font-size: 0.75rem;
            color: #94a3b8;
            margin-bottom: 4px;
        }

        .metric-value {
            font-size: 1.2rem;
            font-weight: bold;
            color: #e2e8f0;
        }

        .metric-small {
            font-size: 0.8rem;
            color: #cbd5e1;
            margin-top: 2px;
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
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.4);
            border: 2px solid #475569;
        }

        .executive-title {
            font-size: 1.4rem;
            font-weight: bold;
            margin-bottom: 15px;
            text-align: center;
            color: #667eea;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }

        .executive-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 12px;
            margin-bottom: 15px;
        }

        .executive-box {
            background: #0f172a;
            padding: 12px 15px;
            border-radius: 8px;
            border-left: 4px solid #667eea;
            transition: transform 0.2s;
        }

        .executive-box:hover {
            transform: translateY(-2px);
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
            font-size: 0.7rem;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }

        .executive-value {
            font-size: 1.3rem;
            font-weight: bold;
            margin-bottom: 3px;
        }

        .executive-desc {
            font-size: 0.75rem;
            color: #cbd5e1;
            line-height: 1.3;
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
        <div class="search-bar" style="margin-top: 50px;">
            <div class="search-section">
                <select id="marketSelector" style="padding: 14px 20px; border-radius: 8px; border: 2px solid #4b5563; background: #1e293b; color: white; font-size: 1rem; margin-right: 10px; cursor: pointer;">
                    <option value="auto">🌐 Auto-detect</option>
                    <option value="idx">🇮🇩 Indonesia (IDX)</option>
                    <option value="us">🇺🇸 United States</option>
                </select>
                <input
                    type="text"
                    id="stockSymbol"
                    placeholder="Enter stock symbol (e.g., BBCA, AAPL, TSLA)"
                    onkeypress="if(event.key==='Enter') loadDashboard()"
                >
                <button onclick="loadDashboard()">Analyze</button>
                <button class="btn-secondary" onclick="window.location.href='/'">Simple View</button>
            </div>

            <!-- 1. Indonesia Suggestions -->
            <div class="quick-picks" style="margin-bottom: 15px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                    <strong style="margin-right: 10px;">🇮🇩 Indonesia - Suggestions:</strong>
                </div>
                <div style="overflow-x: auto; white-space: nowrap;">
                    <button class="quick-pick-btn" onclick="quickAnalyze('BBCA')">BBCA</button>
                    <button class="quick-pick-btn" onclick="quickAnalyze('BBRI')">BBRI</button>
                    <button class="quick-pick-btn" onclick="quickAnalyze('BMRI')">BMRI</button>
                    <button class="quick-pick-btn" onclick="quickAnalyze('TLKM')">TLKM</button>
                    <button class="quick-pick-btn" onclick="quickAnalyze('ASII')">ASII</button>
                    <button class="quick-pick-btn" onclick="quickAnalyze('UNVR')">UNVR</button>
                    <button class="quick-pick-btn" onclick="quickAnalyze('HMSP')">HMSP</button>
                    <button class="quick-pick-btn" onclick="quickAnalyze('ICBP')">ICBP</button>
                </div>
            </div>

            <!-- 2. Indonesia History -->
            <div class="quick-picks" style="margin-bottom: 15px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                    <strong style="margin-right: 10px;">🇮🇩 Indonesia - Recent Searches:</strong>
                </div>
                <div id="idxHistory" style="overflow-x: auto; white-space: nowrap;">
                    <p style="color: #94a3b8; font-size: 0.9rem;">No Indonesia stocks searched yet</p>
                </div>
            </div>

            <!-- 3. US Suggestions -->
            <div class="quick-picks" style="margin-bottom: 15px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                    <strong style="margin-right: 10px;">🇺🇸 United States - Suggestions:</strong>
                </div>
                <div style="overflow-x: auto; white-space: nowrap;">
                    <button class="quick-pick-btn" onclick="quickAnalyze('AAPL', 'us')">AAPL</button>
                    <button class="quick-pick-btn" onclick="quickAnalyze('MSFT', 'us')">MSFT</button>
                    <button class="quick-pick-btn" onclick="quickAnalyze('GOOGL', 'us')">GOOGL</button>
                    <button class="quick-pick-btn" onclick="quickAnalyze('TSLA', 'us')">TSLA</button>
                    <button class="quick-pick-btn" onclick="quickAnalyze('NVDA', 'us')">NVDA</button>
                    <button class="quick-pick-btn" onclick="quickAnalyze('META', 'us')">META</button>
                    <button class="quick-pick-btn" onclick="quickAnalyze('AMZN', 'us')">AMZN</button>
                    <button class="quick-pick-btn" onclick="quickAnalyze('NFLX', 'us')">NFLX</button>
                </div>
            </div>

            <!-- 4. US History -->
            <div class="quick-picks" style="margin-bottom: 15px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                    <strong style="margin-right: 10px;">🇺🇸 United States - Recent Searches:</strong>
                    <button class="quick-pick-btn" style="background: #ef4444; font-size: 0.85rem;" onclick="clearSearchHistory()">Clear All History</button>
                </div>
                <div id="usHistory" style="overflow-x: auto; white-space: nowrap;">
                    <p style="color: #94a3b8; font-size: 0.9rem;">No US stocks searched yet</p>
                </div>
            </div>
        </div>

        <div id="dashboard"></div>
    </div>

    <script>
        let currentSymbol = '';
        let isFavorite = false;

        // Search history management
        function saveToHistory(symbol, market) {
            let history = JSON.parse(localStorage.getItem('searchHistory') || '[]');

            // Remove if already exists (to move to front)
            history = history.filter(item => item.symbol !== symbol);

            // Add to front
            history.unshift({
                symbol: symbol,
                market: market || 'auto',
                timestamp: new Date().toISOString()
            });

            // Keep only last 10
            history = history.slice(0, 10);

            localStorage.setItem('searchHistory', JSON.stringify(history));
            updateHistoryDisplay();
        }

        function getSearchHistory() {
            return JSON.parse(localStorage.getItem('searchHistory') || '[]');
        }

        function clearSearchHistory() {
            if (confirm('Clear all search history?')) {
                localStorage.removeItem('searchHistory');
                updateHistoryDisplay();
            }
        }

        function toggleSections() {
            const historySection = document.getElementById('searchHistorySection');
            const quickPicksSection = document.getElementById('quickPicksSection');

            if (quickPicksSection.style.display === 'none') {
                // Show suggestions, hide history
                historySection.style.display = 'none';
                quickPicksSection.style.display = 'block';
            } else {
                // Show history, hide suggestions
                historySection.style.display = 'block';
                quickPicksSection.style.display = 'none';
                updateHistoryDisplay();
            }
        }

        function updateHistoryDisplay() {
            const history = getSearchHistory();
            const historyDiv = document.getElementById('searchHistory');

            if (history.length === 0) {
                historyDiv.innerHTML = '<p style="color: #94a3b8; font-size: 0.9rem;">No search history yet. Start analyzing stocks!</p>';
                return;
            }

            // Group by market
            const grouped = {
                us: history.filter(item => item.market === 'us'),
                idx: history.filter(item => item.market === 'idx' || item.market === 'auto'),
                other: history.filter(item => item.market !== 'us' && item.market !== 'idx' && item.market !== 'auto')
            };

            let html = '';

            // US Stocks section
            if (grouped.us.length > 0) {
                html += '<div style="margin-bottom: 15px;">';
                html += '<div style="color: #94a3b8; font-size: 0.9rem; margin-bottom: 8px;">🇺🇸 United States:</div>';
                html += grouped.us.map(item => {
                    const timeAgo = getTimeAgo(new Date(item.timestamp));
                    return `
                        <button class="quick-pick-btn" onclick="quickAnalyze('${item.symbol}', '${item.market}')"
                                style="position: relative; padding-right: 45px;">
                            ${item.symbol}
                            <span style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); font-size: 0.75rem; color: #94a3b8;">
                                ${timeAgo}
                            </span>
                        </button>
                    `;
                }).join('');
                html += '</div>';
            }

            // Indonesian Stocks section
            if (grouped.idx.length > 0) {
                html += '<div style="margin-bottom: 15px;">';
                html += '<div style="color: #94a3b8; font-size: 0.9rem; margin-bottom: 8px;">🇮🇩 Indonesia (IDX):</div>';
                html += grouped.idx.map(item => {
                    const timeAgo = getTimeAgo(new Date(item.timestamp));
                    return `
                        <button class="quick-pick-btn" onclick="quickAnalyze('${item.symbol}', '${item.market}')"
                                style="position: relative; padding-right: 45px;">
                            ${item.symbol}
                            <span style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); font-size: 0.75rem; color: #94a3b8;">
                                ${timeAgo}
                            </span>
                        </button>
                    `;
                }).join('');
                html += '</div>';
            }

            // Other markets section (if any)
            if (grouped.other.length > 0) {
                html += '<div style="margin-bottom: 15px;">';
                html += '<div style="color: #94a3b8; font-size: 0.9rem; margin-bottom: 8px;">🌐 Other Markets:</div>';
                html += grouped.other.map(item => {
                    const timeAgo = getTimeAgo(new Date(item.timestamp));
                    return `
                        <button class="quick-pick-btn" onclick="quickAnalyze('${item.symbol}', '${item.market}')"
                                style="position: relative; padding-right: 45px;">
                            ${item.symbol}
                            <span style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); font-size: 0.75rem; color: #94a3b8;">
                                ${timeAgo}
                            </span>
                        </button>
                    `;
                }).join('');
                html += '</div>';
            }

            historyDiv.innerHTML = html;
        }

        function getTimeAgo(date) {
            const seconds = Math.floor((new Date() - date) / 1000);

            if (seconds < 60) return 'just now';
            const minutes = Math.floor(seconds / 60);
            if (minutes < 60) return `${minutes}m ago`;
            const hours = Math.floor(minutes / 60);
            if (hours < 24) return `${hours}h ago`;
            const days = Math.floor(hours / 24);
            return `${days}d ago`;
        }

        async function loadDashboard() {
            const symbol = document.getElementById('stockSymbol').value.trim().toUpperCase();
            const market = document.getElementById('marketSelector').value;

            if (!symbol) {
                alert('Please enter a stock symbol');
                return;
            }

            currentSymbol = symbol;

            // Save to history
            saveToHistory(symbol, market);

            showLoading();

            try {
                const marketParam = market !== 'auto' ? `?market=${market}` : '';
                const response = await fetch(`/api/dashboard/${symbol}${marketParam}`);
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

        function quickAnalyze(symbol, market = null) {
            document.getElementById('stockSymbol').value = symbol;
            if (market && market !== 'auto') {
                document.getElementById('marketSelector').value = market;
            }
            loadDashboard();
        }

        // Initialize on page load - always show history first
        window.addEventListener('DOMContentLoaded', () => {
            updateHistoryDisplay();
        });

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
                ? `<button class="favorite-btn btn-danger" onclick="removeFavorite()">⭐ Remove</button>`
                : `<button class="favorite-btn btn-success" onclick="addFavorite()">☆ Add</button>`;

            // Get key values for executive summary
            const bestEntry = entry_exit.entry_recommendation.conservative || entry_exit.entry_recommendation.moderate;
            const bestExit = entry_exit.exit_recommendation.target_2 || entry_exit.exit_recommendation.target_1;
            const overallRec = overall_analysis.recommendation;
            const accPhase = accumulation.phase;
            const swingRating = swing_analysis.swing_rating;
            const metrics = overall_analysis.metrics || {};

            // Determine executive box classes
            const getExecutiveBoxClass = (action) => {
                if (action.includes('STRONG BUY') || action.includes('BUY')) return 'highlight';
                if (action.includes('SELL')) return 'danger';
                return 'warning';
            };

            // Calculate stop loss and risk-reward
            const stopLoss = (bestEntry.price * 0.95).toFixed(0);
            const riskAmount = bestEntry.price - stopLoss;
            const rewardAmount = bestExit.price - bestEntry.price;
            const riskRewardRatio = riskAmount > 0 ? (rewardAmount / riskAmount).toFixed(2) : 0;

            let html = `
                <!-- EXECUTIVE SUMMARY -->
                <div class="executive-summary">
                    <div class="executive-title">⚡ Decision Dashboard</div>

                    <div class="executive-grid">
                        <!-- Overall Recommendation -->
                        <div class="executive-box ${getExecutiveBoxClass(overallRec.action)}">
                            <div class="executive-label">Action</div>
                            <div class="executive-value" style="color: ${overallRec.color === 'success' ? '#10b981' : overallRec.color === 'danger' ? '#ef4444' : '#f59e0b'}; font-size: 1.5rem;">
                                ${overallRec.action}
                            </div>
                            <div class="executive-desc">Score: ${overall_analysis.score.toFixed(0)}/100 • ${overallRec.confidence}</div>
                        </div>

                        <!-- Entry Price -->
                        <div class="executive-box">
                            <div class="executive-label">💰 Entry Price</div>
                            <div class="executive-value" style="color: #10b981">
                                Rp ${bestEntry.price.toLocaleString()}
                            </div>
                            <div class="executive-desc">${bestEntry.distance_percent > 0 ? '↑' : '↓'} ${Math.abs(bestEntry.distance_percent).toFixed(1)}% from current</div>
                        </div>

                        <!-- Exit Target -->
                        <div class="executive-box">
                            <div class="executive-label">🎯 Exit Target</div>
                            <div class="executive-value" style="color: #3b82f6">
                                Rp ${bestExit.price.toLocaleString()}
                            </div>
                            <div class="executive-desc" style="color: #10b981;">+${bestExit.potential_gain_percent.toFixed(1)}% gain</div>
                        </div>

                        <!-- Stop Loss -->
                        <div class="executive-box">
                            <div class="executive-label">🛑 Stop Loss</div>
                            <div class="executive-value" style="color: #ef4444">
                                Rp ${stopLoss}
                            </div>
                            <div class="executive-desc">5% below entry (ATR-based)</div>
                        </div>

                        <!-- Risk-Reward -->
                        <div class="executive-box ${riskRewardRatio >= 2 ? 'highlight' : ''}">
                            <div class="executive-label">⚖️ Risk:Reward</div>
                            <div class="executive-value" style="color: ${riskRewardRatio >= 2 ? '#10b981' : riskRewardRatio >= 1.5 ? '#f59e0b' : '#ef4444'}">
                                1:${riskRewardRatio}
                            </div>
                            <div class="executive-desc">${riskRewardRatio >= 2 ? 'Excellent' : riskRewardRatio >= 1.5 ? 'Good' : 'Fair'}</div>
                        </div>

                        <!-- Phase -->
                        <div class="executive-box ${accPhase.current_phase === 'ACCUMULATION' ? 'highlight' : accPhase.current_phase === 'DISTRIBUTION' ? 'danger' : ''}">
                            <div class="executive-label">📊 Phase</div>
                            <div class="executive-value" style="color: ${accPhase.current_phase === 'ACCUMULATION' ? '#10b981' : accPhase.current_phase === 'DISTRIBUTION' ? '#ef4444' : '#f59e0b'}; font-size: 1rem;">
                                ${accPhase.current_phase}
                            </div>
                            <div class="executive-desc">Strength: ${accumulation.strength.score}/100</div>
                        </div>

                        <!-- Swing Rating -->
                        <div class="executive-box">
                            <div class="executive-label">📈 Swing</div>
                            <div class="executive-value" style="color: #f59e0b">
                                ${swingRating.score}/100
                            </div>
                            <div class="executive-desc">±${swing_analysis.swing_size.average_swing_percent.toFixed(1)}% avg</div>
                        </div>

                        <!-- Participant Type -->
                        <div class="executive-box ${accumulation.participants.primary_type.includes('Institutional') ? 'highlight' : ''}">
                            <div class="executive-label">👥 Money Type</div>
                            <div class="executive-value" style="color: ${accumulation.participants.primary_type.includes('Institutional') ? '#10b981' : '#f59e0b'}; font-size: 0.9rem;">
                                ${accumulation.participants.primary_type.replace('Dominant', '').replace('Leaning', '')}
                            </div>
                            <div class="executive-desc">${accumulation.participants.institutional_percent.toFixed(0)}% inst</div>
                        </div>
                    </div>

                </div>

                <!-- Stock Header (Compact) -->
                <div class="card" style="padding: 15px; margin-bottom: 15px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
                        <div>
                            <h2 style="font-size: 1.5rem; margin: 0;">${stock_info.name}</h2>
                            <p class="stock-symbol" style="margin: 5px 0;">${stock_info.symbol}</p>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-size: 2rem; font-weight: bold;">Rp ${stock_info.current_price.toLocaleString()}</div>
                            <div class="price-change ${priceChangeClass}" style="font-size: 1rem;">
                                ${priceChangeSign}${stock_info.change_percent.toFixed(2)}%
                            </div>
                        </div>
                        <div>
                            ${favoriteBtn}
                        </div>
                    </div>
                </div>

                <!-- KEY DECISION METRICS (COMPACT) -->
                <div class="compact-grid">
                    <!-- Technical Indicators -->
                    <div class="card">
                        <h3>📈 Technical Indicators</h3>
                        <div class="metric-row" style="grid-template-columns: 1fr 1fr;">
                            <div class="metric">
                                <div class="metric-label">RSI (14)</div>
                                <div class="metric-value" style="color: ${overall_analysis.metrics?.technical?.rsi < 30 ? '#10b981' : overall_analysis.metrics?.technical?.rsi > 70 ? '#ef4444' : '#f59e0b'}">
                                    ${overall_analysis.metrics?.technical?.rsi?.toFixed(0) || 'N/A'}
                                </div>
                                <div class="metric-small">${overall_analysis.metrics?.technical?.rsi < 30 ? 'Oversold' : overall_analysis.metrics?.technical?.rsi > 70 ? 'Overbought' : 'Neutral'}</div>
                            </div>
                            <div class="metric">
                                <div class="metric-label">MA Status</div>
                                <div class="metric-value" style="font-size: 0.85rem; color: ${overall_analysis.metrics?.technical?.above_sma ? '#10b981' : '#ef4444'}">
                                    ${overall_analysis.metrics?.technical?.above_sma ? '↑ Above' : '↓ Below'}
                                </div>
                                <div class="metric-small">20-day SMA</div>
                            </div>
                            <div class="metric">
                                <div class="metric-label">Volatility</div>
                                <div class="metric-value" style="font-size: 0.9rem;">
                                    ${swing_analysis.volatility.volatility_rating}
                                </div>
                                <div class="metric-small">${swing_analysis.volatility.daily_volatility_percent.toFixed(1)}% daily</div>
                            </div>
                            <div class="metric">
                                <div class="metric-label">Trend</div>
                                <div class="metric-value" style="font-size: 0.85rem; color: ${swing_analysis.swing_pattern.pattern.includes('Higher') ? '#10b981' : '#ef4444'}">
                                    ${swing_analysis.swing_pattern.pattern.includes('Higher') ? '📈 Up' : swing_analysis.swing_pattern.pattern.includes('Lower') ? '📉 Down' : '↔️ Side'}
                                </div>
                                <div class="metric-small">${swing_analysis.swing_pattern.pattern.substring(0, 15)}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Fundamental Ratios -->
                    <div class="card">
                        <h3>💰 Fundamentals</h3>
                        <div class="metric-row" style="grid-template-columns: 1fr 1fr;">
                            <div class="metric">
                                <div class="metric-label">P/E Ratio</div>
                                <div class="metric-value" style="color: ${metrics.valuation?.pe_ratio < 15 ? '#10b981' : metrics.valuation?.pe_ratio < 25 ? '#f59e0b' : '#ef4444'}">
                                    ${metrics.valuation?.pe_ratio?.toFixed(1) || 'N/A'}
                                </div>
                                <div class="metric-small">${metrics.valuation?.pe_ratio < 15 ? 'Cheap' : metrics.valuation?.pe_ratio < 25 ? 'Fair' : 'Expensive'}</div>
                            </div>
                            <div class="metric">
                                <div class="metric-label">P/B Ratio</div>
                                <div class="metric-value" style="color: ${metrics.valuation?.pb_ratio < 1.5 ? '#10b981' : metrics.valuation?.pb_ratio < 3 ? '#f59e0b' : '#ef4444'}">
                                    ${metrics.valuation?.pb_ratio?.toFixed(1) || 'N/A'}
                                </div>
                                <div class="metric-small">${metrics.valuation?.pb_ratio < 1.5 ? 'Underval' : metrics.valuation?.pb_ratio < 3 ? 'Fair' : 'Overval'}</div>
                            </div>
                            <div class="metric">
                                <div class="metric-label">ROE</div>
                                <div class="metric-value" style="color: ${metrics.profitability?.roe > 15 ? '#10b981' : metrics.profitability?.roe > 10 ? '#f59e0b' : '#ef4444'}">
                                    ${metrics.profitability?.roe?.toFixed(1) || 'N/A'}%
                                </div>
                                <div class="metric-small">${metrics.profitability?.roe > 15 ? 'Strong' : metrics.profitability?.roe > 10 ? 'Good' : 'Weak'}</div>
                            </div>
                            <div class="metric">
                                <div class="metric-label">EPS</div>
                                <div class="metric-value" style="font-size: 0.9rem; color: ${metrics.profitability?.eps > 0 ? '#10b981' : '#ef4444'}">
                                    ${metrics.profitability?.eps?.toFixed(0) || 'N/A'}
                                </div>
                                <div class="metric-small">${metrics.profitability?.eps > 0 ? 'Profit' : 'Loss'}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Volume & Liquidity -->
                    <div class="card">
                        <h3>📊 Volume & Liquidity</h3>
                        <div class="metric-row" style="grid-template-columns: 1fr 1fr;">
                            <div class="metric">
                                <div class="metric-label">Volume Ratio</div>
                                <div class="metric-value" style="color: ${accumulation.current_volume_vs_average.ratio > 1.2 ? '#10b981' : accumulation.current_volume_vs_average.ratio > 0.8 ? '#f59e0b' : '#ef4444'}">
                                    ${accumulation.current_volume_vs_average.ratio}x
                                </div>
                                <div class="metric-small">${accumulation.current_volume_vs_average.status}</div>
                            </div>
                            <div class="metric">
                                <div class="metric-label">Market Cap</div>
                                <div class="metric-value" style="font-size: 0.85rem;">
                                    ${metrics.valuation?.market_cap ? (metrics.valuation.market_cap / 1000000000000).toFixed(1) + 'T' : 'N/A'}
                                </div>
                                <div class="metric-small">${metrics.valuation?.market_cap > 100000000000000 ? 'Large' : metrics.valuation?.market_cap > 10000000000000 ? 'Mid' : 'Small'}</div>
                            </div>
                            <div class="metric">
                                <div class="metric-label">Accum Duration</div>
                                <div class="metric-value" style="color: ${accumulation.duration.days > 15 ? '#10b981' : '#f59e0b'}">
                                    ${accumulation.duration.days}d
                                </div>
                                <div class="metric-small">${accumulation.duration.status}</div>
                            </div>
                            <div class="metric">
                                <div class="metric-label">Magnitude</div>
                                <div class="metric-value" style="font-size: 0.9rem; color: ${accumulation.magnitude.vs_average_percent > 25 ? '#10b981' : '#f59e0b'}">
                                    ${accumulation.magnitude.size}
                                </div>
                                <div class="metric-small">${accumulation.magnitude.vs_average_percent > 0 ? '+' : ''}${accumulation.magnitude.vs_average_percent}%</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Accumulation Analysis (Compact) -->
                <div class="compact-grid">
                    <!-- Accumulation Phase Card -->
                    <div class="card" style="grid-column: span 2;">
                        <div style="background: ${getPhaseColor(accumulation.phase.current_phase)}; padding: 15px; border-radius: 8px; text-align: center;">
                            <div style="font-size: 1.3rem; font-weight: bold; margin-bottom: 8px;">${accumulation.phase.current_phase}</div>
                            <div style="font-size: 0.85rem; opacity: 0.95;">${accumulation.phase.description}</div>
                        </div>
                        <h3 style="margin-top: 15px;">📊 Accumulation Metrics</h3>
                        <div class="metric-row" style="grid-template-columns: 1fr 1fr 1fr;">
                            <div class="metric">
                                <div class="metric-label">Strength</div>
                                <div class="metric-value">${accumulation.strength.score}</div>
                                <div class="metric-small">${accumulation.strength.strength}</div>
                            </div>
                            <div class="metric">
                                <div class="metric-label">OBV</div>
                                <div class="metric-value" style="font-size: 0.9rem;">${accumulation.obv_analysis.trend}</div>
                                <div class="metric-small">${accumulation.obv_analysis.interpretation.substring(0, 12)}</div>
                            </div>
                            <div class="metric">
                                <div class="metric-label">Vol Ratio</div>
                                <div class="metric-value">${accumulation.current_volume_vs_average.ratio}x</div>
                                <div class="metric-small">${accumulation.current_volume_vs_average.status.substring(0, 10)}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Participant Analysis -->
                    <div class="card">
                        <h3>👥 Participants</h3>
                        <div style="text-align: center; margin: 15px 0;">
                            <div style="font-size: 1.1rem; font-weight: bold; color: ${accumulation.participants.primary_type.includes('Institutional') ? '#10b981' : '#f59e0b'};">
                                ${accumulation.participants.primary_type.replace('Dominant', '').replace('Leaning', '')}
                            </div>
                        </div>
                        <div style="display: flex; gap: 8px; margin: 12px 0;">
                            <div style="flex: ${accumulation.participants.institutional_percent}; background: #10b981; height: 25px; border-radius: 4px; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: bold;">
                                ${accumulation.participants.institutional_percent >= 15 ? accumulation.participants.institutional_percent.toFixed(0) + '%' : ''}
                            </div>
                            <div style="flex: ${accumulation.participants.retail_percent}; background: #ef4444; height: 25px; border-radius: 4px; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: bold;">
                                ${accumulation.participants.retail_percent >= 15 ? accumulation.participants.retail_percent.toFixed(0) + '%' : ''}
                            </div>
                        </div>
                        <div style="font-size: 0.75rem; color: #94a3b8; text-align: center;">
                            🏦 ${accumulation.participants.institutional_percent.toFixed(0)}% | 👤 ${accumulation.participants.retail_percent.toFixed(0)}%
                        </div>
                    </div>
                </div>


                <!-- Entry/Exit & Swing (Compact 3-column) -->
                <div class="compact-grid">
                    <!-- Entry Zones -->
                    <div class="card">
                        <h3>🎯 Entry Zones</h3>
                        ${Object.entries(entry_exit.entry_recommendation).map(([type, zone]) => `
                            <div style="background: #0f172a; padding: 10px; border-radius: 6px; margin-bottom: 8px; border-left: 3px solid #10b981;">
                                <div style="font-size: 0.75rem; color: #94a3b8; text-transform: uppercase;">${type}</div>
                                <div style="font-size: 1.1rem; font-weight: bold; margin: 4px 0;">Rp ${zone.price.toLocaleString()}</div>
                                <div style="font-size: 0.75rem; color: ${zone.distance_percent > 0 ? '#10b981' : '#ef4444'};">
                                    ${zone.distance_percent > 0 ? '↑' : '↓'} ${Math.abs(zone.distance_percent).toFixed(1)}% from current
                                </div>
                            </div>
                        `).join('')}
                        <div style="margin-top: 10px; padding: 10px; background: #0f172a; border-radius: 6px; font-size: 0.8rem;">
                            <strong>Action:</strong> ${entry_exit.position_recommendation.recommended_action}
                        </div>
                    </div>

                    <!-- Exit Targets -->
                    <div class="card">
                        <h3>🚀 Exit Targets</h3>
                        ${Object.entries(entry_exit.exit_recommendation).map(([type, zone]) => `
                            <div style="background: #0f172a; padding: 10px; border-radius: 6px; margin-bottom: 8px; border-left: 3px solid #3b82f6;">
                                <div style="font-size: 0.75rem; color: #94a3b8; text-transform: uppercase;">${type}</div>
                                <div style="font-size: 1.1rem; font-weight: bold; margin: 4px 0;">Rp ${zone.price.toLocaleString()}</div>
                                <div style="font-size: 0.75rem; color: #10b981;">
                                    ↑ +${zone.potential_gain_percent.toFixed(1)}% gain
                                </div>
                            </div>
                        `).join('')}
                    </div>

                    <!-- Swing Analysis -->
                    <div class="card">
                        <h3>📈 Swing Analysis</h3>
                        <div style="text-align: center; padding: 12px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 8px; margin-bottom: 12px;">
                            <div style="font-size: 1.8rem; font-weight: bold;">${swing_analysis.swing_rating.score}/100</div>
                            <div style="font-size: 0.85rem; opacity: 0.95;">${swing_analysis.swing_rating.rating}</div>
                        </div>
                        <div class="metric-row" style="grid-template-columns: 1fr 1fr;">
                            <div class="metric">
                                <div class="metric-label">Avg Swing</div>
                                <div class="metric-value" style="font-size: 1rem;">${swing_analysis.swing_size.average_swing_percent.toFixed(1)}%</div>
                            </div>
                            <div class="metric">
                                <div class="metric-label">Pattern</div>
                                <div class="metric-value" style="font-size: 0.75rem;">${swing_analysis.swing_pattern.pattern.includes('Higher') ? '📈' : swing_analysis.swing_pattern.pattern.includes('Lower') ? '📉' : '↔️'}</div>
                            </div>
                        </div>
                        <div style="margin-top: 10px; font-size: 0.75rem; color: #94a3b8;">
                            <strong>Bollinger:</strong> ${swing_analysis.bollinger_bands.squeeze_status}<br>
                            <strong>Position:</strong> ${swing_analysis.bollinger_bands.price_position_percent.toFixed(0)}% in bands
                        </div>
                    </div>
                </div>

                <!-- Support & Resistance (Compact) -->
                <div class="compact-grid">
                    <div class="card">
                        <h3 style="color: #10b981;">📍 Support</h3>
                        ${entry_exit.support_levels.length > 0
                            ? entry_exit.support_levels.map((level, i) => `
                                <div style="background: #0f172a; padding: 8px 10px; border-radius: 4px; margin-bottom: 6px; border-left: 3px solid #10b981; display: flex; justify-content: space-between;">
                                    <span style="font-size: 0.75rem; color: #94a3b8;">S${i + 1}</span>
                                    <strong style="font-size: 0.9rem;">Rp ${level.toLocaleString()}</strong>
                                </div>
                            `).join('')
                            : '<p style="color: #94a3b8; font-size: 0.8rem;">No support detected</p>'
                        }
                    </div>

                    <div class="card">
                        <h3 style="color: #ef4444;">📍 Resistance</h3>
                        ${entry_exit.resistance_levels.length > 0
                            ? entry_exit.resistance_levels.map((level, i) => `
                                <div style="background: #0f172a; padding: 8px 10px; border-radius: 4px; margin-bottom: 6px; border-left: 3px solid #ef4444; display: flex; justify-content: space-between;">
                                    <span style="font-size: 0.75rem; color: #94a3b8;">R${i + 1}</span>
                                    <strong style="font-size: 0.9rem;">Rp ${level.toLocaleString()}</strong>
                                </div>
                            `).join('')
                            : '<p style="color: #94a3b8; font-size: 0.8rem;">No resistance detected</p>'
                        }
                    </div>

                    <div class="card">
                        <h3>🔢 Fibonacci</h3>
                        <div style="font-size: 0.75rem;">
                            ${Object.entries(entry_exit.fibonacci_levels).slice(0, 5).map(([level, price]) => `
                                <div style="display: flex; justify-content: space-between; padding: 4px 0; border-bottom: 1px solid #334155;">
                                    <span style="color: #94a3b8;">${level.replace('level_', '').replace('_', '.')}%</span>
                                    <strong>Rp ${price.toLocaleString()}</strong>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                </div>

                <!-- Analysis Categories (Compact) -->
                <div class="card">
                    <h3>📋 Analysis Breakdown</h3>
                    <div class="metric-row" style="grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));">
                        ${Object.entries(overall_analysis.analysis).map(([category, data]) => `
                            <div class="metric">
                                <div class="metric-label">${category.substring(0, 12)}</div>
                                <div class="metric-value" style="font-size: 1.1rem;">${data.score.toFixed(0)}%</div>
                                <div style="margin-top: 5px; height: 5px; background: #0f172a; border-radius: 2px; overflow: hidden;">
                                    <div style="width: ${data.score}%; height: 100%; background: ${data.score >= 70 ? '#10b981' : data.score >= 50 ? '#f59e0b' : '#ef4444'};"></div>
                                </div>
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
