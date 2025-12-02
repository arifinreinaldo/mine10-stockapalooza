<div class="card"
     x-data="{
         open: $persist(true).as('widget-buy-opportunities'),
         loading: false,
         data: null,
         error: null,
         async loadData(forceRefresh = false) {
             this.loading = true;
             this.error = null;
             try {
                 const url = forceRefresh ? '/api/scan-opportunities?refresh=true' : '/api/scan-opportunities';
                 const response = await fetch(url, {
                     headers: {
                         'Accept': 'application/json',
                         'Accept-Language': $store.language.current
                     }
                 });
                 const result = await response.json();
                 if (result.success !== false) {
                     this.data = result;
                 } else {
                     this.error = result.message || 'Failed to load data';
                 }
             } catch (err) {
                 this.error = err.message;
             } finally {
                 this.loading = false;
             }
         }
     }"
     x-init="loadData()">

    <!-- Widget Header -->
    <div class="widget-header">
        <button @click="open = !open" class="flex items-center gap-2 flex-1">
            <span class="text-2xl">🎯</span>
            <h3 class="text-lg font-semibold" x-text="$store.language.t('buyOpportunities')"></h3>
            <svg class="w-5 h-5 transition-transform duration-200"
                 :class="{'rotate-180': !open}"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>
        <button @click="loadData(true)"
                :disabled="loading"
                class="p-2 hover:bg-dark rounded-lg transition-colors">
            <span class="text-xl" :class="{'animate-spin': loading}">🔄</span>
        </button>
    </div>

    <!-- Widget Content -->
    <div x-show="open" x-collapse>
        <!-- Description -->
        <p class="text-sm text-gray-400 mb-4" x-text="$store.language.t('buyOpportunitiesDesc')"></p>

        <!-- Loading State -->
        <div x-show="loading" class="text-center py-8">
            <div class="spinner mx-auto mb-4"></div>
            <p class="text-gray-400" x-text="$store.language.t('loading')"></p>
        </div>

        <!-- Error State -->
        <div x-show="error && !loading" class="bg-danger/20 border border-danger rounded-lg p-4 mb-4">
            <p class="text-danger" x-text="error"></p>
            <button @click="loadData()" class="btn-secondary mt-2">
                <span x-text="$store.language.t('tryAgain')"></span>
            </button>
        </div>

        <!-- Data Display -->
        <div x-show="!loading && !error && data">
            <!-- Scan Info -->
            <div x-show="data && data.opportunities_found !== undefined" class="flex items-center justify-between mb-4 p-3 bg-dark rounded-lg">
                <div>
                    <span class="text-success font-bold text-2xl" x-text="data?.opportunities_found || 0"></span>
                    <span class="text-gray-400 ml-2" x-text="$store.language.t('opportunitiesFound')"></span>
                </div>
                <div class="text-sm text-gray-400">
                    <span x-text="$store.language.t('stocksScanned')"></span>:
                    <span class="font-semibold" x-text="data?.scanned || 0"></span>
                </div>
            </div>

            <!-- Opportunities Grid -->
            <div x-show="data && data.data && data.data.length > 0"
                 class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <template x-for="(stock, index) in data.data" :key="stock.symbol">
                    <div @click="$store.dashboard.loadStock(stock.symbol, stock.market || 'auto')"
                         class="bg-dark rounded-lg p-4 border-2 border-dark-lighter hover:border-primary-500
                                transition-all cursor-pointer group">

                        <!-- Header -->
                        <div class="flex items-start justify-between mb-3">
                            <div>
                                <h4 class="font-bold text-lg group-hover:text-primary-500 transition-colors"
                                    x-text="stock.symbol"></h4>
                                <p class="text-xs text-gray-400 line-clamp-1" x-text="stock.name || ''"></p>
                            </div>
                            <span class="stock-badge badge-buy text-xs px-2 py-1"
                                  x-text="stock.signal || 'BUY'"></span>
                        </div>

                        <!-- Price -->
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <p class="text-2xl font-bold" x-text="stock.current_price?.toLocaleString() || 'N/A'"></p>
                                <p class="text-xs text-gray-400" x-text="$store.language.t('currentPrice')"></p>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold"
                                   :class="stock.change_percent >= 0 ? 'text-success' : 'text-danger'"
                                   x-text="(stock.change_percent >= 0 ? '+' : '') + (stock.change_percent?.toFixed(2) || '0') + '%'"></p>
                                <p class="text-xs text-gray-400" x-text="$store.language.t('change')"></p>
                            </div>
                        </div>

                        <!-- Metrics -->
                        <div class="grid grid-cols-2 gap-2 text-xs">
                            <div x-show="stock.macd_signal">
                                <p class="text-gray-400">MACD</p>
                                <p class="font-semibold"
                                   :class="stock.macd_signal?.toLowerCase().includes('buy') ? 'text-success' : 'text-warning'"
                                   x-text="stock.macd_signal"></p>
                            </div>
                            <div x-show="stock.divergence">
                                <p class="text-gray-400" x-text="$store.language.t('divergence')"></p>
                                <p class="font-semibold text-primary-500" x-text="stock.divergence"></p>
                            </div>
                            <div x-show="stock.confidence !== undefined">
                                <p class="text-gray-400" x-text="$store.language.t('confidence')"></p>
                                <p class="font-semibold text-primary-500" x-text="stock.confidence + '%'"></p>
                            </div>
                            <div x-show="stock.potential_gain">
                                <p class="text-gray-400" x-text="$store.language.t('potentialGain')"></p>
                                <p class="font-semibold text-success" x-text="'+' + stock.potential_gain + '%'"></p>
                            </div>
                        </div>

                        <!-- Click hint -->
                        <div class="mt-3 pt-3 border-t border-dark-lighter text-center">
                            <p class="text-xs text-gray-400 group-hover:text-primary-500 transition-colors">
                                👆 <span x-text="$store.language.t('viewDetails')"></span>
                            </p>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Empty State -->
            <div x-show="data && (!data.data || data.data.length === 0)"
                 class="text-center py-8 text-gray-400">
                <p class="text-4xl mb-4">🔍</p>
                <p x-text="$store.language.t('noOpportunitiesFound')"></p>
            </div>

            <!-- Last Update -->
            <div x-show="data && data.cached_at" class="mt-4 text-xs text-gray-400 text-center">
                <span x-text="$store.language.t('lastUpdate')"></span>:
                <span x-text="data.cached_at"></span>
            </div>
        </div>
    </div>
</div>
