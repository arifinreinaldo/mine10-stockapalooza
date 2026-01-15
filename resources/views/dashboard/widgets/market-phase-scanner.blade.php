<div class="card"
     x-data="{
         open: $persist(true).as('widget-market-phase-scanner'),
         loading: false,
         data: null,
         error: null,
         activePhase: 'all',
         showHistory: false,
         history: null,
         historyLoading: false,

         async loadData(forceRefresh = false) {
             this.loading = true;
             this.error = null;
             try {
                 const url = forceRefresh
                     ? '/api/scan-market-phases?market=idx&limit=10&refresh=true'
                     : '/api/scan-market-phases?market=idx&limit=10';
                 const response = await fetch(url, {
                     headers: {
                         'Accept': 'application/json',
                         'Accept-Language': $store.language?.current || 'en'
                     }
                 });
                 const result = await response.json();
                 if (result.success !== false) {
                     this.data = result;
                 } else {
                     this.error = result.message || 'Failed to load phase data';
                 }
             } catch (err) {
                 this.error = err.message;
             } finally {
                 this.loading = false;
             }
         },

         async loadHistory() {
             this.historyLoading = true;
             try {
                 const response = await fetch('/api/analysis-history?limit=15');
                 const result = await response.json();
                 if (result.success) {
                     this.history = result;
                 }
             } catch (err) {
                 console.error('Failed to load history:', err);
             } finally {
                 this.historyLoading = false;
             }
         },

         getPhaseIcon(phase) {
             const icons = {
                 'MARKUP': '📈',
                 'MARKDOWN': '📉',
                 'DISTRIBUTION': '🔻',
                 'ACCUMULATION': '💰'
             };
             return icons[phase] || '❓';
         },

         getPhaseColor(phase) {
             const colors = {
                 'MARKUP': 'text-success',
                 'MARKDOWN': 'text-danger',
                 'DISTRIBUTION': 'text-warning',
                 'ACCUMULATION': 'text-primary-500'
             };
             return colors[phase] || 'text-gray-400';
         },

         getPhaseBgColor(phase) {
             const colors = {
                 'MARKUP': 'bg-success/20 border-success',
                 'MARKDOWN': 'bg-danger/20 border-danger',
                 'DISTRIBUTION': 'bg-warning/20 border-warning',
                 'ACCUMULATION': 'bg-primary-500/20 border-primary-500'
             };
             return colors[phase] || 'bg-gray-500/20 border-gray-500';
         },

         getPhaseDescription(phase) {
             const descriptions = {
                 'MARKUP': 'Price rising with volume support. Uptrend in progress.',
                 'MARKDOWN': 'Price falling with volume. Downtrend in progress.',
                 'DISTRIBUTION': 'Smart money distributing at higher prices.',
                 'ACCUMULATION': 'Smart money accumulating at lower prices.'
             };
             return descriptions[phase] || '';
         }
     }"
     x-init="loadData()">

    <!-- Widget Header -->
    <div class="widget-header">
        <button @click="open = !open" class="flex items-center gap-2 flex-1">
            <span class="text-2xl">🔄</span>
            <h3 class="text-lg font-semibold">Market Phase Scanner</h3>
            <svg class="w-5 h-5 transition-transform duration-200"
                 :class="{'rotate-180': !open}"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>
        <div class="flex items-center gap-2">
            <button @click="showHistory = !showHistory; if(showHistory && !history) loadHistory()"
                    class="p-2 hover:bg-dark rounded-lg transition-colors"
                    :class="{'bg-primary-500/20': showHistory}"
                    title="View Analysis History">
                <span class="text-xl">📋</span>
            </button>
            <button @click="loadData(true)"
                    :disabled="loading"
                    class="p-2 hover:bg-dark rounded-lg transition-colors"
                    title="Refresh Data">
                <span class="text-xl" :class="{'animate-spin': loading}">🔄</span>
            </button>
        </div>
    </div>

    <!-- Widget Content -->
    <div x-show="open" x-collapse>
        <!-- Description -->
        <p class="text-sm text-gray-400 mb-4">
            Wyckoff market cycle analysis - Top 10 stocks in each phase.
            <span class="text-success font-semibold">MARKUP</span> = uptrend,
            <span class="text-danger font-semibold">MARKDOWN</span> = downtrend,
            <span class="text-warning font-semibold">DISTRIBUTION</span> = topping,
            <span class="text-primary-500 font-semibold">ACCUMULATION</span> = bottoming.
        </p>

        <!-- Phase Filter Tabs -->
        <div class="flex flex-wrap gap-2 mb-4">
            <button @click="activePhase = 'all'"
                    class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all"
                    :class="activePhase === 'all' ? 'bg-primary-500 text-white' : 'bg-dark-lighter text-gray-300 hover:bg-dark'">
                All Phases
            </button>
            <template x-for="phase in ['MARKUP', 'ACCUMULATION', 'DISTRIBUTION', 'MARKDOWN']" :key="phase">
                <button @click="activePhase = phase"
                        class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all flex items-center gap-1 border-2"
                        :class="activePhase === phase ? getPhaseBgColor(phase) : 'bg-dark-lighter text-gray-300 hover:bg-dark border-transparent'">
                    <span x-text="getPhaseIcon(phase)"></span>
                    <span x-text="phase"></span>
                    <span class="ml-1 text-xs opacity-70"
                          x-text="data?.phase_counts?.[phase] ? `(${data.phase_counts[phase]})` : ''"></span>
                </button>
            </template>
        </div>

        <!-- Loading State -->
        <div x-show="loading" class="text-center py-8">
            <div class="spinner mx-auto mb-4"></div>
            <p class="text-gray-400">Scanning market phases...</p>
        </div>

        <!-- Error State -->
        <div x-show="error && !loading" class="bg-danger/20 border border-danger rounded-lg p-4 mb-4">
            <p class="text-danger" x-text="error"></p>
            <button @click="loadData()" class="btn-secondary mt-2">Try Again</button>
        </div>

        <!-- History Panel (Slide-in) -->
        <div x-show="showHistory" x-collapse class="mb-4 bg-dark rounded-lg p-4 border border-dark-lighter">
            <div class="flex items-center justify-between mb-3">
                <h4 class="font-semibold text-primary-500">📋 Analysis History (Last 3 months)</h4>
                <button @click="showHistory = false" class="text-gray-400 hover:text-white text-xl">&times;</button>
            </div>

            <!-- Phase Summary -->
            <div x-show="history?.phase_summary" class="grid grid-cols-4 gap-2 mb-4">
                <template x-for="phase in ['MARKUP', 'ACCUMULATION', 'DISTRIBUTION', 'MARKDOWN']" :key="phase">
                    <div class="text-center p-2 rounded-lg" :class="getPhaseBgColor(phase)">
                        <span x-text="getPhaseIcon(phase)"></span>
                        <p class="text-lg font-bold" x-text="history?.phase_summary?.[phase] || 0"></p>
                        <p class="text-xs opacity-70" x-text="phase"></p>
                    </div>
                </template>
            </div>

            <div x-show="historyLoading" class="text-center py-4">
                <div class="spinner mx-auto"></div>
            </div>

            <div x-show="!historyLoading && history?.history?.length > 0" class="space-y-2 max-h-60 overflow-y-auto custom-scrollbar">
                <template x-for="item in history.history" :key="item.id">
                    <div @click="$store.dashboard?.loadStock(item.symbol, item.market)"
                         class="flex items-center justify-between p-2 bg-dark-lighter rounded-lg cursor-pointer hover:bg-dark transition-colors">
                        <div class="flex items-center gap-2">
                            <span x-text="getPhaseIcon(item.phase)"></span>
                            <div>
                                <span class="font-semibold" x-text="item.symbol"></span>
                                <span class="text-xs text-gray-400 ml-2" x-text="item.stock_name?.substring(0, 20)"></span>
                            </div>
                        </div>
                        <div class="text-right text-xs">
                            <div :class="getPhaseColor(item.phase)" x-text="item.phase"></div>
                            <div class="text-gray-500" x-text="new Date(item.created_at).toLocaleDateString()"></div>
                        </div>
                    </div>
                </template>
            </div>

            <div x-show="!historyLoading && (!history?.history || history.history.length === 0)"
                 class="text-center py-4 text-gray-400">
                <p>No analysis history yet. Analyze some stocks to build your history!</p>
            </div>
        </div>

        <!-- Phase Data Display -->
        <div x-show="!loading && !error && data" class="space-y-4">

            <template x-for="phase in ['MARKUP', 'ACCUMULATION', 'DISTRIBUTION', 'MARKDOWN']" :key="phase">
                <div x-show="(activePhase === 'all' || activePhase === phase) && data?.phases?.[phase]?.length > 0"
                     class="border-2 rounded-lg overflow-hidden"
                     :class="getPhaseBgColor(phase)">

                    <!-- Phase Header -->
                    <div class="p-3 border-b border-white/10">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="text-xl" x-text="getPhaseIcon(phase)"></span>
                                <span class="font-bold" :class="getPhaseColor(phase)" x-text="phase"></span>
                                <span class="text-xs text-gray-400">(<span x-text="data?.phases?.[phase]?.length || 0"></span> stocks)</span>
                            </div>
                        </div>
                        <p class="text-xs text-gray-400 mt-1" x-text="getPhaseDescription(phase)"></p>
                    </div>

                    <!-- Stocks List -->
                    <div class="divide-y divide-white/5">
                        <template x-for="(stock, idx) in data?.phases?.[phase] || []" :key="stock.symbol">
                            <div @click="$store.dashboard?.loadStock(stock.symbol, stock.market)"
                                 class="p-3 hover:bg-white/5 cursor-pointer transition-colors flex items-center justify-between">

                                <div class="flex items-center gap-3">
                                    <span class="text-xs text-gray-500 font-mono w-4" x-text="idx + 1"></span>
                                    <div>
                                        <div class="font-semibold" x-text="stock.symbol"></div>
                                        <div class="text-xs text-gray-400 truncate max-w-[120px]" x-text="stock.name"></div>
                                    </div>
                                </div>

                                <div class="flex items-center gap-4 text-sm">
                                    <div class="text-right">
                                        <div class="font-mono" x-text="stock.price?.toLocaleString()"></div>
                                        <div class="text-xs"
                                             :class="stock.change_percent >= 0 ? 'text-success' : 'text-danger'"
                                             x-text="(stock.change_percent >= 0 ? '+' : '') + stock.change_percent + '%'"></div>
                                    </div>

                                    <div class="text-right hidden sm:block">
                                        <div class="text-xs text-gray-400">Score</div>
                                        <div class="font-semibold" x-text="stock.score + '/100'"></div>
                                    </div>

                                    <div class="text-right hidden md:block">
                                        <div class="text-xs text-gray-400">Inst.</div>
                                        <div class="text-primary-500 font-semibold"
                                             x-text="(stock.institutional_percent || 0).toFixed(0) + '%'"></div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </template>

            <!-- Empty State -->
            <div x-show="!data?.phases || (
                    (!data.phases.MARKUP || data.phases.MARKUP.length === 0) &&
                    (!data.phases.MARKDOWN || data.phases.MARKDOWN.length === 0) &&
                    (!data.phases.DISTRIBUTION || data.phases.DISTRIBUTION.length === 0) &&
                    (!data.phases.ACCUMULATION || data.phases.ACCUMULATION.length === 0)
                 )"
                 class="text-center py-8 text-gray-400">
                <p class="text-4xl mb-4">🔍</p>
                <p>No stocks in defined phases. Try refreshing the scan.</p>
            </div>

            <!-- Scan Info Footer -->
            <div class="flex items-center justify-between text-xs text-gray-400 pt-2 border-t border-dark-lighter">
                <span>Scanned: <span x-text="data?.scanned || 0"></span> stocks</span>
                <span x-text="data?.cached_at || ''"></span>
            </div>
        </div>
    </div>
</div>
