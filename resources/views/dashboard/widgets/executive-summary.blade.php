<!-- Executive Summary Widget -->
<div class="card">
    <div class="widget-header">
        <h2 class="text-2xl font-bold flex items-center gap-2">
            <span>📊</span>
            <span x-text="$store.language.t('executiveSummary')"></span>
        </h2>
    </div>

    <div x-show="$store.dashboard.stockData && $store.dashboard.stockData.data">
        <!-- Stock Header -->
        <div class="mb-6 p-6 bg-gradient-to-r from-primary-500/20 to-purple-600/20 rounded-xl border border-primary-500/50">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h3 class="text-3xl font-bold"
                        x-text="$store.dashboard.stockData?.data?.stock_info?.symbol"></h3>
                    <p class="text-gray-400"
                       x-text="$store.dashboard.stockData?.data?.stock_info?.name"></p>
                </div>
                <div class="text-right">
                    <p class="text-4xl font-bold"
                       x-text="$store.dashboard.stockData?.data?.stock_info?.current_price?.toLocaleString()"></p>
                    <p class="text-lg"
                       :class="$store.dashboard.stockData?.data?.stock_info?.change_percent >= 0 ? 'text-success' : 'text-danger'"
                       x-text="($store.dashboard.stockData?.data?.stock_info?.change_percent >= 0 ? '+' : '') +
                               $store.dashboard.stockData?.data?.stock_info?.change_percent?.toFixed(2) + '%'"></p>
                </div>
            </div>
        </div>

        <!-- Overall Recommendation -->
        <div class="mb-6 p-6 rounded-xl"
             :class="{
                 'bg-success/20 border-2 border-success': $store.dashboard.stockData?.data?.overall_analysis?.recommendation?.action?.includes('STRONG BUY'),
                 'bg-success/10 border-2 border-success/50': $store.dashboard.stockData?.data?.overall_analysis?.recommendation?.action?.includes('BUY') && !$store.dashboard.stockData?.data?.overall_analysis?.recommendation?.action?.includes('STRONG'),
                 'bg-warning/20 border-2 border-warning': $store.dashboard.stockData?.data?.overall_analysis?.recommendation?.action?.includes('HOLD'),
                 'bg-danger/20 border-2 border-danger': $store.dashboard.stockData?.data?.overall_analysis?.recommendation?.action?.includes('SELL')
             }">
            <div class="text-center">
                <p class="text-sm text-gray-400 mb-2" x-text="$store.language.t('recommendation')"></p>
                <p class="text-4xl font-bold mb-4"
                   :class="{
                       'text-success': $store.dashboard.stockData?.data?.overall_analysis?.recommendation?.action?.includes('BUY'),
                       'text-warning': $store.dashboard.stockData?.data?.overall_analysis?.recommendation?.action?.includes('HOLD'),
                       'text-danger': $store.dashboard.stockData?.data?.overall_analysis?.recommendation?.action?.includes('SELL')
                   }"
                   x-text="$store.dashboard.stockData?.data?.overall_analysis?.recommendation?.action"></p>
                <div class="flex items-center justify-center gap-2">
                    <span class="text-gray-400" x-text="$store.language.t('score')"></span>
                    <span class="text-2xl font-bold"
                          x-text="$store.dashboard.stockData?.data?.overall_analysis?.score"></span>
                    <span class="text-gray-400">/100</span>
                </div>
            </div>
        </div>

        <!-- Key Metrics Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
            <!-- Entry Price -->
            <div class="metric-card" x-show="$store.dashboard.stockData?.data?.entry_exit?.entry_price">
                <p class="text-sm text-gray-400 mb-1" x-text="$store.language.t('entryPrice')"></p>
                <p class="text-2xl font-bold text-success"
                   x-text="$store.dashboard.stockData?.data?.entry_exit?.entry_price?.toLocaleString()"></p>
                <p class="text-xs text-gray-400"
                   x-show="$store.dashboard.stockData?.data?.entry_exit?.distance_from_entry"
                   x-text="$store.dashboard.stockData?.data?.entry_exit?.distance_from_entry + '% ' + $store.language.t('fromCurrent')"></p>
            </div>

            <!-- Exit Target -->
            <div class="metric-card" x-show="$store.dashboard.stockData?.data?.entry_exit?.exit_target">
                <p class="text-sm text-gray-400 mb-1" x-text="$store.language.t('exitPrice')"></p>
                <p class="text-2xl font-bold text-primary-500"
                   x-text="$store.dashboard.stockData?.data?.entry_exit?.exit_target?.toLocaleString()"></p>
                <p class="text-xs text-success"
                   x-show="$store.dashboard.stockData?.data?.entry_exit?.potential_gain"
                   x-text="'+' + $store.dashboard.stockData?.data?.entry_exit?.potential_gain + '% ' + $store.language.t('potentialGain')"></p>
            </div>

            <!-- Stop Loss -->
            <div class="metric-card" x-show="$store.dashboard.stockData?.data?.entry_exit?.stop_loss">
                <p class="text-sm text-gray-400 mb-1" x-text="$store.language.t('stopLoss')"></p>
                <p class="text-2xl font-bold text-danger"
                   x-text="$store.dashboard.stockData?.data?.entry_exit?.stop_loss?.toLocaleString()"></p>
                <p class="text-xs text-gray-400"
                   x-show="$store.dashboard.stockData?.data?.entry_exit?.stop_loss_percent"
                   x-text="$store.dashboard.stockData?.data?.entry_exit?.stop_loss_percent + '% ' + $store.language.t('below')"></p>
            </div>

            <!-- Risk/Reward -->
            <div class="metric-card" x-show="$store.dashboard.stockData?.data?.entry_exit?.risk_reward_ratio">
                <p class="text-sm text-gray-400 mb-1" x-text="$store.language.t('riskReward')"></p>
                <p class="text-2xl font-bold text-warning"
                   x-text="'1:' + $store.dashboard.stockData?.data?.entry_exit?.risk_reward_ratio"></p>
            </div>

            <!-- Market Phase -->
            <div class="metric-card" x-show="$store.dashboard.stockData?.data?.overall_analysis?.market_phase">
                <p class="text-sm text-gray-400 mb-1" x-text="$store.language.t('marketPhase')"></p>
                <p class="text-xl font-bold text-primary-500"
                   x-text="$store.dashboard.stockData?.data?.overall_analysis?.market_phase"></p>
            </div>

            <!-- Swing Rating -->
            <div class="metric-card" x-show="$store.dashboard.stockData?.data?.swing_analysis?.rating">
                <p class="text-sm text-gray-400 mb-1" x-text="$store.language.t('swingRating')"></p>
                <p class="text-2xl font-bold"
                   :class="{
                       'text-success': $store.dashboard.stockData?.data?.swing_analysis?.rating >= 7,
                       'text-warning': $store.dashboard.stockData?.data?.swing_analysis?.rating >= 5 && $store.dashboard.stockData?.data?.swing_analysis?.rating < 7,
                       'text-danger': $store.dashboard.stockData?.data?.swing_analysis?.rating < 5
                   }"
                   x-text="$store.dashboard.stockData?.data?.swing_analysis?.rating + '/10'"></p>
            </div>
        </div>

        <!-- Additional Metrics -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <!-- Participant Type -->
            <div class="text-center p-3 bg-dark rounded-lg"
                 x-show="$store.dashboard.stockData?.data?.overall_analysis?.participant_money_type">
                <p class="text-xs text-gray-400 mb-1" x-text="$store.language.t('participantType')"></p>
                <p class="font-semibold text-sm"
                   x-text="$store.dashboard.stockData?.data?.overall_analysis?.participant_money_type"></p>
            </div>

            <!-- MACD Signal -->
            <div class="text-center p-3 bg-dark rounded-lg"
                 x-show="$store.dashboard.stockData?.data?.overall_analysis?.macd_momentum">
                <p class="text-xs text-gray-400 mb-1">MACD</p>
                <p class="font-semibold text-sm"
                   :class="{
                       'text-success': $store.dashboard.stockData?.data?.overall_analysis?.macd_momentum?.includes('Bullish'),
                       'text-danger': $store.dashboard.stockData?.data?.overall_analysis?.macd_momentum?.includes('Bearish')
                   }"
                   x-text="$store.dashboard.stockData?.data?.overall_analysis?.macd_momentum"></p>
            </div>

            <!-- Divergence -->
            <div class="text-center p-3 bg-dark rounded-lg"
                 x-show="$store.dashboard.stockData?.data?.overall_analysis?.divergence_alert">
                <p class="text-xs text-gray-400 mb-1" x-text="$store.language.t('divergence')"></p>
                <p class="font-semibold text-sm text-warning"
                   x-text="$store.dashboard.stockData?.data?.overall_analysis?.divergence_alert"></p>
            </div>

            <!-- 52-Week Position -->
            <div class="text-center p-3 bg-dark rounded-lg"
                 x-show="$store.dashboard.stockData?.data?.stock_info?.position_52w">
                <p class="text-xs text-gray-400 mb-1">52W Position</p>
                <p class="font-semibold text-sm"
                   x-text="$store.dashboard.stockData?.data?.stock_info?.position_52w + '%'"></p>
            </div>
        </div>

        <!-- Favorite Button -->
        <div class="mt-6 flex justify-center">
            <button @click="$store.favorites.toggle(
                        $store.dashboard.stockData?.data?.stock_info?.symbol,
                        $store.dashboard.stockData?.data?.stock_info?.name
                    )"
                    class="btn-primary flex items-center gap-2">
                <span x-show="!$store.favorites.isFavorite($store.dashboard.stockData?.data?.stock_info?.symbol)">
                    ⭐ <span x-text="$store.language.t('addToFavorites')"></span>
                </span>
                <span x-show="$store.favorites.isFavorite($store.dashboard.stockData?.data?.stock_info?.symbol)">
                    ⭐ <span x-text="$store.language.t('removeFromFavorites')"></span>
                </span>
            </button>
        </div>
    </div>

    <!-- Empty State -->
    <div x-show="!$store.dashboard.stockData" class="text-center py-12 text-gray-400">
        <p class="text-4xl mb-4">🔍</p>
        <p x-text="$store.language.t('searchToStart')"></p>
    </div>
</div>
