<!-- Whale Flow (Bandarmology) Widget -->
<div class="card"
     x-data="{
         open: $persist(true).as('widget-whale-flow')
     }">
    <div class="widget-header">
        <button @click="open = !open" class="flex items-center gap-2 flex-1">
            <span class="text-2xl">🐋</span>
            <h3 class="text-lg font-semibold" x-text="$store.language.t('whaleFlow')"></h3>
            <svg class="w-5 h-5 transition-transform duration-200"
                 :class="{'rotate-180': !open}"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>
    </div>

    <div x-show="open" x-collapse>
        <div x-show="$store.dashboard.stockData && $store.dashboard.stockData.data">
            <!-- Phase Banner -->
            <div class="mb-6 p-6 rounded-xl border-2"
                 :class="{
                     'bg-success/20 border-success': $store.dashboard.stockData?.data?.bandarmology?.phase?.phase === 'AKUMULASI',
                     'bg-primary-500/20 border-primary-500': $store.dashboard.stockData?.data?.bandarmology?.phase?.phase === 'MARK-UP',
                     'bg-warning/20 border-warning': $store.dashboard.stockData?.data?.bandarmology?.phase?.phase === 'DISTRIBUSI',
                     'bg-danger/20 border-danger': $store.dashboard.stockData?.data?.bandarmology?.phase?.phase === 'MARK-DOWN',
                     'bg-gray-700 border-gray-600': !['AKUMULASI', 'MARK-UP', 'DISTRIBUSI', 'MARK-DOWN'].includes($store.dashboard.stockData?.data?.bandarmology?.phase?.phase)
                 }">
                <div class="text-center">
                    <p class="text-sm text-gray-400 mb-2">Market Phase</p>
                    <p class="text-3xl font-bold mb-2"
                       x-text="$store.dashboard.stockData?.data?.bandarmology?.phase?.icon + ' ' + $store.dashboard.stockData?.data?.bandarmology?.phase?.phase"></p>
                    <p class="text-sm"
                       x-text="$store.dashboard.stockData?.data?.bandarmology?.phase?.description"></p>
                    <p class="text-xs text-gray-400 mt-2"
                       x-text="$store.dashboard.stockData?.data?.bandarmology?.phase?.actionHint"></p>
                </div>
            </div>

            <!-- Main Metrics Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <!-- Bandar Strength -->
                <div class="metric-card">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm text-gray-400">Bandar Strength</span>
                        <span class="text-3xl font-bold"
                              :class="{
                                  'text-success': $store.dashboard.stockData?.data?.bandarmology?.bandar_strength?.score >= 70,
                                  'text-warning': $store.dashboard.stockData?.data?.bandarmology?.bandar_strength?.score >= 40 && $store.dashboard.stockData?.data?.bandarmology?.bandar_strength?.score < 70,
                                  'text-danger': $store.dashboard.stockData?.data?.bandarmology?.bandar_strength?.score < 40
                              }"
                              x-text="$store.dashboard.stockData?.data?.bandarmology?.bandar_strength?.score"></span>
                    </div>
                    <!-- Progress Bar -->
                    <div class="h-4 bg-dark rounded-full overflow-hidden mb-2">
                        <div class="h-full transition-all duration-500"
                             :class="{
                                 'bg-success': $store.dashboard.stockData?.data?.bandarmology?.bandar_strength?.score >= 70,
                                 'bg-warning': $store.dashboard.stockData?.data?.bandarmology?.bandar_strength?.score >= 40 && $store.dashboard.stockData?.data?.bandarmology?.bandar_strength?.score < 70,
                                 'bg-danger': $store.dashboard.stockData?.data?.bandarmology?.bandar_strength?.score < 40
                             }"
                             :style="`width: ${$store.dashboard.stockData?.data?.bandarmology?.bandar_strength?.score}%`"></div>
                    </div>
                    <p class="text-xs text-gray-400"
                       x-text="$store.dashboard.stockData?.data?.bandarmology?.bandar_strength?.strength"></p>
                </div>

                <!-- Breakout Probability -->
                <div class="metric-card">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm text-gray-400">Breakout Probability</span>
                        <span class="text-3xl font-bold"
                              :class="{
                                  'text-success': $store.dashboard.stockData?.data?.bandarmology?.breakout_probability?.probability >= 70,
                                  'text-warning': $store.dashboard.stockData?.data?.bandarmology?.breakout_probability?.probability >= 40 && $store.dashboard.stockData?.data?.bandarmology?.breakout_probability?.probability < 70,
                                  'text-gray-300': $store.dashboard.stockData?.data?.bandarmology?.breakout_probability?.probability < 40
                              }"
                              x-text="$store.dashboard.stockData?.data?.bandarmology?.breakout_probability?.probability + '%'"></span>
                    </div>
                    <div class="h-4 bg-dark rounded-full overflow-hidden mb-2">
                        <div class="h-full bg-primary-500 transition-all duration-500"
                             :style="`width: ${$store.dashboard.stockData?.data?.bandarmology?.breakout_probability?.probability}%`"></div>
                    </div>
                    <p class="text-xs text-gray-400"
                       x-text="$store.dashboard.stockData?.data?.bandarmology?.breakout_probability?.reasoning"></p>
                </div>
            </div>

            <!-- Signals & Smart Money Flow -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <!-- Buy/Sell Signals -->
                <div class="metric-card">
                    <p class="text-sm text-gray-400 mb-3">Trading Signals</p>
                    <div class="space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-gray-400">Primary Signal:</span>
                            <span class="px-3 py-1 rounded font-bold"
                                  :class="{
                                      'bg-success/20 text-success': $store.dashboard.stockData?.data?.bandarmology?.signals?.primary_signal === 'BUY',
                                      'bg-danger/20 text-danger': $store.dashboard.stockData?.data?.bandarmology?.signals?.primary_signal === 'SELL',
                                      'bg-gray-700 text-gray-300': $store.dashboard.stockData?.data?.bandarmology?.signals?.primary_signal === 'HOLD'
                                  }"
                                  x-text="$store.dashboard.stockData?.data?.bandarmology?.signals?.primary_signal"></span>
                        </div>
                        <div class="text-xs space-y-1" x-show="$store.dashboard.stockData?.data?.bandarmology?.signals?.supporting_signals?.length > 0">
                            <p class="text-gray-400 mb-1">Supporting Signals:</p>
                            <template x-for="signal in $store.dashboard.stockData?.data?.bandarmology?.signals?.supporting_signals" :key="signal">
                                <div class="flex items-center gap-2">
                                    <span class="text-success">✓</span>
                                    <span x-text="signal"></span>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Smart Money Flow -->
                <div class="metric-card">
                    <p class="text-sm text-gray-400 mb-3">Smart Money Flow</p>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-gray-400">Direction:</span>
                            <span class="font-bold text-lg"
                                  :class="{
                                      'text-success': $store.dashboard.stockData?.data?.bandarmology?.smart_money_flow?.direction === 'INFLOW',
                                      'text-danger': $store.dashboard.stockData?.data?.bandarmology?.smart_money_flow?.direction === 'OUTFLOW',
                                      'text-gray-300': $store.dashboard.stockData?.data?.bandarmology?.smart_money_flow?.direction === 'NEUTRAL'
                                  }"
                                  x-text="$store.dashboard.stockData?.data?.bandarmology?.smart_money_flow?.direction"></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-gray-400">Strength:</span>
                            <span class="font-semibold"
                                  x-text="$store.dashboard.stockData?.data?.bandarmology?.smart_money_flow?.strength"></span>
                        </div>
                        <p class="text-xs text-gray-400 italic"
                           x-text="$store.dashboard.stockData?.data?.bandarmology?.smart_money_flow?.interpretation"></p>
                    </div>
                </div>
            </div>

            <!-- Divergence Analysis -->
            <div class="metric-card mb-6" x-show="$store.dashboard.stockData?.data?.bandarmology?.divergence?.detected">
                <p class="text-sm text-gray-400 mb-3">Price-Volume Divergence</p>
                <div class="space-y-2">
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-gray-400">Type:</span>
                        <span class="font-bold"
                              :class="{
                                  'text-success': $store.dashboard.stockData?.data?.bandarmology?.divergence?.type === 'Bullish',
                                  'text-danger': $store.dashboard.stockData?.data?.bandarmology?.divergence?.type === 'Bearish'
                              }"
                              x-text="$store.dashboard.stockData?.data?.bandarmology?.divergence?.type"></span>
                    </div>
                    <p class="text-xs text-gray-400"
                       x-text="$store.dashboard.stockData?.data?.bandarmology?.divergence?.interpretation"></p>
                </div>
            </div>

            <!-- Risk Assessment -->
            <div class="metric-card">
                <p class="text-sm text-gray-400 mb-3">Risk Assessment</p>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-gray-400">Risk Level:</span>
                        <span class="px-3 py-1 rounded font-bold"
                              :class="{
                                  'bg-success/20 text-success': $store.dashboard.stockData?.data?.bandarmology?.risk_assessment?.risk_level === 'LOW',
                                  'bg-warning/20 text-warning': $store.dashboard.stockData?.data?.bandarmology?.risk_assessment?.risk_level === 'MEDIUM',
                                  'bg-danger/20 text-danger': $store.dashboard.stockData?.data?.bandarmology?.risk_assessment?.risk_level === 'HIGH'
                              }"
                              x-text="$store.dashboard.stockData?.data?.bandarmology?.risk_assessment?.risk_level"></span>
                    </div>
                    <div class="space-y-1" x-show="$store.dashboard.stockData?.data?.bandarmology?.risk_assessment?.factors?.length > 0">
                        <p class="text-xs text-gray-400 mb-1">Risk Factors:</p>
                        <template x-for="factor in $store.dashboard.stockData?.data?.bandarmology?.risk_assessment?.factors" :key="factor">
                            <div class="flex items-center gap-2 text-xs">
                                <span class="text-warning">⚠</span>
                                <span x-text="factor"></span>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Recommendation -->
            <div class="mt-6 p-6 rounded-xl border-2"
                 :class="{
                     'bg-success/20 border-success': $store.dashboard.stockData?.data?.bandarmology?.recommendation?.action === 'BUY',
                     'bg-warning/20 border-warning': $store.dashboard.stockData?.data?.bandarmology?.recommendation?.action === 'HOLD',
                     'bg-danger/20 border-danger': $store.dashboard.stockData?.data?.bandarmology?.recommendation?.action === 'SELL'
                 }">
                <div class="text-center">
                    <p class="text-sm text-gray-400 mb-2">Bandarmology Recommendation</p>
                    <p class="text-2xl font-bold mb-3"
                       :class="{
                           'text-success': $store.dashboard.stockData?.data?.bandarmology?.recommendation?.action === 'BUY',
                           'text-warning': $store.dashboard.stockData?.data?.bandarmology?.recommendation?.action === 'HOLD',
                           'text-danger': $store.dashboard.stockData?.data?.bandarmology?.recommendation?.action === 'SELL'
                       }"
                       x-text="$store.dashboard.stockData?.data?.bandarmology?.recommendation?.action"></p>
                    <p class="text-sm mb-2"
                       x-text="$store.dashboard.stockData?.data?.bandarmology?.recommendation?.reasoning"></p>
                    <div class="flex justify-center items-center gap-2 mt-3">
                        <span class="text-gray-400">Confidence:</span>
                        <span class="font-bold"
                              x-text="$store.dashboard.stockData?.data?.bandarmology?.recommendation?.confidence"></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Empty State -->
        <div x-show="!$store.dashboard.stockData" class="text-center py-8 text-gray-400">
            <p class="text-4xl mb-4">🐋</p>
            <p x-text="$store.language.t('searchToStart')"></p>
        </div>
    </div>
</div>
