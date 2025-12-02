<div class="card"
     x-data="{
         open: $persist(true).as('widget-institutional'),
         loading: false,
         data: null,
         error: null,
         minInstitutional: 60,
         async loadData() {
             this.loading = true;
             this.error = null;
             try {
                 const response = await fetch(`/api/scan-institutional-stocks?market=idx&min_institutional=${this.minInstitutional}`, {
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
            <span class="text-2xl">🐋</span>
            <h3 class="text-lg font-semibold" x-text="$store.language.t('institutionalStocks')"></h3>
            <svg class="w-5 h-5 transition-transform duration-200"
                 :class="{'rotate-180': !open}"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>
        <button @click="loadData()"
                :disabled="loading"
                class="p-2 hover:bg-dark rounded-lg transition-colors">
            <span class="text-xl" :class="{'animate-spin': loading}">🔄</span>
        </button>
    </div>

    <!-- Widget Content -->
    <div x-show="open" x-collapse>
        <!-- Description -->
        <p class="text-sm text-gray-400 mb-4" x-text="$store.language.t('institutionalStocksDesc')"></p>

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
            <div x-show="data && data.institutional_stocks_found !== undefined"
                 class="flex items-center justify-between mb-4 p-3 bg-dark rounded-lg">
                <div>
                    <span class="text-success font-bold text-2xl" x-text="data?.institutional_stocks_found || 0"></span>
                    <span class="text-gray-400 ml-2">Institutional Stocks</span>
                </div>
                <div class="text-xs text-gray-400">
                    Min: <span class="font-semibold" x-text="data?.min_institutional_threshold || 60"></span>%
                </div>
            </div>

            <!-- Stocks Grid -->
            <div x-show="data && data.top_10 && data.top_10.length > 0"
                 class="space-y-3">
                <template x-for="(stock, index) in data.top_10" :key="stock.symbol">
                    <div @click="$store.dashboard.loadStock(stock.symbol, 'idx')"
                         class="bg-dark rounded-lg p-4 border-2 border-dark-lighter hover:border-success
                                transition-all cursor-pointer group relative overflow-hidden">

                        <!-- Institutional Percentage Background -->
                        <div class="absolute inset-0 bg-gradient-to-r from-success/10 to-transparent opacity-50"
                             :style="`width: ${stock.institutional_percent || 0}%`"></div>

                        <!-- Content -->
                        <div class="relative z-10">
                            <!-- Header -->
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2">
                                        <span class="text-lg">🏛️</span>
                                        <h4 class="font-bold text-lg group-hover:text-success transition-colors"
                                            x-text="stock.symbol"></h4>
                                    </div>
                                    <p class="text-xs text-gray-400 line-clamp-1 ml-7" x-text="stock.name || ''"></p>
                                </div>
                                <div class="text-right ml-2">
                                    <p class="text-lg font-bold" x-text="stock.current_price?.toLocaleString() || 'N/A'"></p>
                                    <p class="text-sm"
                                       :class="stock.change_percent >= 0 ? 'text-success' : 'text-danger'"
                                       x-text="(stock.change_percent >= 0 ? '+' : '') + (stock.change_percent?.toFixed(2) || '0') + '%'"></p>
                                </div>
                            </div>

                            <!-- Institutional Percentage (Main Metric) -->
                            <div class="mb-3 p-3 bg-success/20 border border-success rounded-lg">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-300" x-text="$store.language.t('institutionalPercent')"></span>
                                    <span class="text-2xl font-bold text-success" x-text="(stock.institutional_percent || 0) + '%'"></span>
                                </div>
                                <!-- Progress Bar -->
                                <div class="mt-2 h-2 bg-dark rounded-full overflow-hidden">
                                    <div class="h-full bg-success transition-all duration-500"
                                         :style="`width: ${stock.institutional_percent || 0}%`"></div>
                                </div>
                            </div>

                            <!-- Additional Metrics -->
                            <div class="grid grid-cols-2 gap-2 text-xs">
                                <div x-show="stock.accumulation_phase">
                                    <p class="text-gray-400" x-text="$store.language.t('accumulationPhase')"></p>
                                    <p class="font-semibold text-primary-500" x-text="stock.accumulation_phase"></p>
                                </div>
                                <div x-show="stock.volume_pattern">
                                    <p class="text-gray-400">Volume Pattern</p>
                                    <p class="font-semibold text-warning" x-text="stock.volume_pattern"></p>
                                </div>
                                <div x-show="stock.action">
                                    <p class="text-gray-400">Action</p>
                                    <p class="font-semibold"
                                       :class="stock.action?.toLowerCase().includes('buy') ? 'text-success' :
                                               stock.action?.toLowerCase().includes('hold') ? 'text-warning' :
                                               'text-danger'"
                                       x-text="stock.action"></p>
                                </div>
                                <div x-show="stock.strength !== undefined">
                                    <p class="text-gray-400" x-text="$store.language.t('accumulationStrength')"></p>
                                    <p class="font-semibold text-success" x-text="stock.strength + '%'"></p>
                                </div>
                            </div>

                            <!-- Ranking Badge -->
                            <div class="absolute top-4 right-4 w-8 h-8 bg-primary-500 rounded-full
                                        flex items-center justify-center font-bold text-sm"
                                 x-text="index + 1"></div>

                            <!-- Click hint -->
                            <div class="mt-3 pt-3 border-t border-dark-lighter text-center">
                                <p class="text-xs text-gray-400 group-hover:text-success transition-colors">
                                    👆 <span x-text="$store.language.t('viewDetails')"></span>
                                </p>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Empty State -->
            <div x-show="data && (!data.top_10 || data.top_10.length === 0)"
                 class="text-center py-8 text-gray-400">
                <p class="text-4xl mb-4">🐋</p>
                <p x-text="$store.language.t('noData')"></p>
            </div>

            <!-- Last Scan Info -->
            <div x-show="data && data.cached_at" class="mt-4 text-xs text-gray-400 text-center">
                <span x-text="$store.language.t('lastUpdate')"></span>:
                <span x-text="data.cached_at"></span>
            </div>
        </div>
    </div>
</div>
