import Alpine from 'alpinejs';

Alpine.store('dashboard', {
    currentSymbol: null,
    currentMarket: 'auto',
    stockData: null,
    loading: false,
    error: null,

    // Favorites sidebar state
    favoritesOpen: false,

    // Load dashboard data for a stock
    async loadStock(symbol, market = 'auto') {
        this.loading = true;
        this.error = null;
        this.currentSymbol = symbol;
        this.currentMarket = market;

        try {
            const locale = Alpine.store('language').current;
            const response = await fetch(`/api/dashboard/${symbol}?market=${market}`, {
                headers: {
                    'Accept': 'application/json',
                    'Accept-Language': locale,
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                }
            });

            if (!response.ok) {
                throw new Error('Failed to load stock data');
            }

            const result = await response.json();

            if (result.success !== false) {
                this.stockData = result;

                // Dispatch event
                window.dispatchEvent(new CustomEvent('stock-loaded', {
                    detail: { symbol, market, data: result }
                }));

                // Add to search history
                this.addToHistory(symbol, market);

                return result;
            } else {
                this.error = result.message || 'Failed to load stock data';
                return null;
            }
        } catch (err) {
            this.error = err.message;
            console.error('Error loading stock:', err);
            return null;
        } finally {
            this.loading = false;
        }
    },

    // Add to search history
    addToHistory(symbol, market) {
        const history = this.getHistory(market);

        // Remove if already exists
        const filtered = history.filter(item => item.symbol !== symbol);

        // Add to beginning
        filtered.unshift({
            symbol,
            market,
            timestamp: new Date().toISOString()
        });

        // Keep only last 10
        const limited = filtered.slice(0, 10);

        // Save to localStorage
        localStorage.setItem(`history_${market}`, JSON.stringify(limited));
    },

    // Get search history for a market
    getHistory(market) {
        try {
            const stored = localStorage.getItem(`history_${market}`);
            return stored ? JSON.parse(stored) : [];
        } catch (err) {
            console.error('Error reading history:', err);
            return [];
        }
    },

    // Clear search history for a market
    clearHistory(market) {
        localStorage.removeItem(`history_${market}`);
        window.dispatchEvent(new CustomEvent('history-cleared', {
            detail: { market }
        }));
    },

    // Toggle favorites sidebar
    toggleFavorites() {
        this.favoritesOpen = !this.favoritesOpen;
    }
});
