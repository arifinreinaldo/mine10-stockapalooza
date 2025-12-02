<!-- Technical Indicators Widget -->
<div class="card"
     x-data="{
         open: $persist(true).as('widget-technical-indicators')
     }">
    <div class="widget-header">
        <button @click="open = !open" class="flex items-center gap-2 flex-1">
            <span class="text-2xl">📈</span>
            <h3 class="text-lg font-semibold" x-text="$store.language.t('technicalIndicators')"></h3>
            <svg class="w-5 h-5 transition-transform duration-200"
                 :class="{'rotate-180': !open}"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>
    </div>

    <div x-show="open" x-collapse>
        <div x-show="$store.dashboard.stockData && $store.dashboard.stockData.data">
            <!-- Indicators Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- RSI -->
                <div class="metric-card" x-show="$store.dashboard.stockData?.data?.overall_analysis?.metrics?.technical?.rsi">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm text-gray-400">RSI (14)</span>
                        <span class="text-2xl font-bold"
                              :class="{
                                  'text-danger': $store.dashboard.stockData?.data?.overall_analysis?.metrics?.technical?.rsi > 70,
                                  'text-success': $store.dashboard.stockData?.data?.overall_analysis?.metrics?.technical?.rsi < 30,
                                  'text-gray-300': $store.dashboard.stockData?.data?.overall_analysis?.metrics?.technical?.rsi >= 30 && $store.dashboard.stockData?.data?.overall_analysis?.metrics?.technical?.rsi <= 70
                              }"
                              x-text="$store.dashboard.stockData?.data?.overall_analysis?.metrics?.technical?.rsi?.toFixed(2)"></span>
                    </div>
                    <!-- RSI Bar -->
                    <div class="h-3 bg-dark rounded-full overflow-hidden mb-2">
                        <div class="h-full transition-all duration-500"
                             :class="{
                                 'bg-danger': $store.dashboard.stockData?.data?.overall_analysis?.metrics?.technical?.rsi > 70,
                                 'bg-success': $store.dashboard.stockData?.data?.overall_analysis?.metrics?.technical?.rsi < 30,
                                 'bg-gray-500': $store.dashboard.stockData?.data?.overall_analysis?.metrics?.technical?.rsi >= 30 && $store.dashboard.stockData?.data?.overall_analysis?.metrics?.technical?.rsi <= 70
                             }"
                             :style="`width: ${$store.dashboard.stockData?.data?.overall_analysis?.metrics?.technical?.rsi}%`"></div>
                    </div>
                    <p class="text-xs text-gray-400 text-center">
                        <span x-show="$store.dashboard.stockData?.data?.overall_analysis?.metrics?.technical?.rsi > 70" class="text-danger">Overbought</span>
                        <span x-show="$store.dashboard.stockData?.data?.overall_analysis?.metrics?.technical?.rsi < 30" class="text-success">Oversold</span>
                        <span x-show="$store.dashboard.stockData?.data?.overall_analysis?.metrics?.technical?.rsi >= 30 && $store.dashboard.stockData?.data?.overall_analysis?.metrics?.technical?.rsi <= 70">Neutral</span>
                    </p>
                </div>

                <!-- MACD -->
                <div class="metric-card" x-show="$store.dashboard.stockData?.data?.overall_analysis?.metrics?.technical?.macd">
                    <p class="text-sm text-gray-400 mb-2">MACD</p>
                    <div class="space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-gray-400">Signal:</span>
                            <span class="text-lg font-bold px-3 py-1 rounded"
                                  :class="{
                                      'bg-success/20 text-success': $store.dashboard.stockData?.data?.overall_analysis?.metrics?.technical?.macd?.signal?.includes('BUY'),
                                      'bg-danger/20 text-danger': $store.dashboard.stockData?.data?.overall_analysis?.metrics?.technical?.macd?.signal?.includes('SELL'),
                                      'bg-gray-700 text-gray-300': $store.dashboard.stockData?.data?.overall_analysis?.metrics?.technical?.macd?.signal?.includes('HOLD')
                                  }"
                                  x-text="$store.dashboard.stockData?.data?.overall_analysis?.metrics?.technical?.macd?.signal"></span>
                        </div>
                        <div class="text-xs space-y-1">
                            <div class="flex justify-between">
                                <span class="text-gray-400">MACD:</span>
                                <span x-text="$store.dashboard.stockData?.data?.overall_analysis?.metrics?.technical?.macd?.macd_line?.toFixed(2)"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-400">Signal:</span>
                                <span x-text="$store.dashboard.stockData?.data?.overall_analysis?.metrics?.technical?.macd?.signal_line?.toFixed(2)"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Moving Average -->
                <div class="metric-card" x-show="$store.dashboard.stockData?.data?.overall_analysis?.metrics?.technical?.above_sma !== undefined">
                    <p class="text-sm text-gray-400 mb-2" x-text="$store.language.t('movingAverage')"></p>
                    <p class="text-xl font-bold"
                       :class="{
                           'text-success': $store.dashboard.stockData?.data?.overall_analysis?.metrics?.technical?.above_sma === true,
                           'text-danger': $store.dashboard.stockData?.data?.overall_analysis?.metrics?.technical?.above_sma === false
                       }"
                       x-text="$store.dashboard.stockData?.data?.overall_analysis?.metrics?.technical?.above_sma ? 'Above SMA(20)' : 'Below SMA(20)'"></p>
                    <p class="text-xs text-gray-400 mt-1"
                       x-text="$store.dashboard.stockData?.data?.overall_analysis?.metrics?.technical?.above_sma ? 'Bullish signal' : 'Bearish signal'"></p>
                </div>

                <!-- Stochastic -->
                <div class="metric-card" x-show="$store.dashboard.stockData?.data?.overall_analysis?.metrics?.technical?.stochastic">
                    <p class="text-sm text-gray-400 mb-2">Stochastic</p>
                    <div class="space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-gray-400">%K:</span>
                            <span class="text-lg font-bold"
                                  :class="{
                                      'text-danger': $store.dashboard.stockData?.data?.overall_analysis?.metrics?.technical?.stochastic?.k > 80,
                                      'text-success': $store.dashboard.stockData?.data?.overall_analysis?.metrics?.technical?.stochastic?.k < 20,
                                      'text-gray-300': $store.dashboard.stockData?.data?.overall_analysis?.metrics?.technical?.stochastic?.k >= 20 && $store.dashboard.stockData?.data?.overall_analysis?.metrics?.technical?.stochastic?.k <= 80
                                  }"
                                  x-text="$store.dashboard.stockData?.data?.overall_analysis?.metrics?.technical?.stochastic?.k?.toFixed(2)"></span>
                        </div>
                        <span class="text-xs px-2 py-1 rounded"
                              :class="{
                                  'bg-danger/20 text-danger': $store.dashboard.stockData?.data?.overall_analysis?.metrics?.technical?.stochastic?.k > 80,
                                  'bg-success/20 text-success': $store.dashboard.stockData?.data?.overall_analysis?.metrics?.technical?.stochastic?.k < 20,
                                  'bg-gray-700 text-gray-300': $store.dashboard.stockData?.data?.overall_analysis?.metrics?.technical?.stochastic?.k >= 20 && $store.dashboard.stockData?.data?.overall_analysis?.metrics?.technical?.stochastic?.k <= 80
                              }"
                              x-text="$store.dashboard.stockData?.data?.overall_analysis?.metrics?.technical?.stochastic?.k > 80 ? 'Overbought' : ($store.dashboard.stockData?.data?.overall_analysis?.metrics?.technical?.stochastic?.k < 20 ? 'Oversold' : 'Neutral')"></span>
                    </div>
                </div>

                <!-- MFI (Money Flow Index) -->
                <div class="metric-card" x-show="$store.dashboard.stockData?.data?.overall_analysis?.metrics?.technical?.mfi">
                    <p class="text-sm text-gray-400 mb-2">MFI</p>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-2xl font-bold"
                              :class="{
                                  'text-danger': $store.dashboard.stockData?.data?.overall_analysis?.metrics?.technical?.mfi > 80,
                                  'text-success': $store.dashboard.stockData?.data?.overall_analysis?.metrics?.technical?.mfi < 20,
                                  'text-gray-300': $store.dashboard.stockData?.data?.overall_analysis?.metrics?.technical?.mfi >= 20 && $store.dashboard.stockData?.data?.overall_analysis?.metrics?.technical?.mfi <= 80
                              }"
                              x-text="$store.dashboard.stockData?.data?.overall_analysis?.metrics?.technical?.mfi?.toFixed(2)"></span>
                    </div>
                    <p class="text-xs text-gray-400">Money Flow strength indicator</p>
                </div>

                <!-- Divergence -->
                <div class="metric-card" x-show="$store.dashboard.stockData?.data?.overall_analysis?.metrics?.technical?.divergence?.divergence !== 'NONE'">
                    <p class="text-sm text-gray-400 mb-2" x-text="$store.language.t('divergence')"></p>
                    <p class="text-lg font-bold"
                       :class="{
                           'text-success': $store.dashboard.stockData?.data?.overall_analysis?.metrics?.technical?.divergence?.divergence === 'BULLISH',
                           'text-danger': $store.dashboard.stockData?.data?.overall_analysis?.metrics?.technical?.divergence?.divergence === 'BEARISH'
                       }"
                       x-text="$store.dashboard.stockData?.data?.overall_analysis?.metrics?.technical?.divergence?.divergence"></p>
                    <p class="text-xs text-gray-400 mt-1"
                       x-text="$store.dashboard.stockData?.data?.overall_analysis?.metrics?.technical?.divergence?.significance"></p>
                </div>

                <!-- 52-Week Position -->
                <div class="metric-card" x-show="$store.dashboard.stockData?.data?.overall_analysis?.metrics?.technical?.['52_week']">
                    <p class="text-sm text-gray-400 mb-2">52-Week Position</p>
                    <div class="space-y-2">
                        <div class="text-lg font-bold"
                             :class="{
                                 'text-success': $store.dashboard.stockData?.data?.overall_analysis?.metrics?.technical?.['52_week']?.position === 'Near Low',
                                 'text-warning': $store.dashboard.stockData?.data?.overall_analysis?.metrics?.technical?.['52_week']?.position === 'Middle',
                                 'text-danger': $store.dashboard.stockData?.data?.overall_analysis?.metrics?.technical?.['52_week']?.position === 'Near High'
                             }"
                             x-text="$store.dashboard.stockData?.data?.overall_analysis?.metrics?.technical?.['52_week']?.position"></div>
                        <div class="text-xs space-y-1">
                            <div class="flex justify-between">
                                <span class="text-gray-400">High:</span>
                                <span x-text="$store.dashboard.stockData?.data?.overall_analysis?.metrics?.technical?.['52_week']?.high?.toLocaleString()"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-400">Low:</span>
                                <span x-text="$store.dashboard.stockData?.data?.overall_analysis?.metrics?.technical?.['52_week']?.low?.toLocaleString()"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Fundamentals Section (if available) -->
            <div x-show="$store.dashboard.stockData?.data?.stock_info?.pe_ratio ||
                         $store.dashboard.stockData?.data?.stock_info?.pb_ratio ||
                         $store.dashboard.stockData?.data?.stock_info?.roe ||
                         $store.dashboard.stockData?.data?.stock_info?.dividend_yield"
                 class="mt-6">
                <h4 class="text-lg font-semibold mb-4 flex items-center gap-2">
                    <span>💼</span>
                    <span x-text="$store.language.t('fundamentals')"></span>
                </h4>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    <!-- P/E Ratio -->
                    <div class="text-center p-3 bg-dark rounded-lg" x-show="$store.dashboard.stockData?.data?.stock_info?.pe_ratio">
                        <p class="text-xs text-gray-400 mb-1" x-text="$store.language.t('peRatio')"></p>
                        <p class="text-lg font-bold"
                           x-text="$store.dashboard.stockData?.data?.stock_info?.pe_ratio"></p>
                    </div>

                    <!-- P/B Ratio -->
                    <div class="text-center p-3 bg-dark rounded-lg" x-show="$store.dashboard.stockData?.data?.stock_info?.pb_ratio">
                        <p class="text-xs text-gray-400 mb-1" x-text="$store.language.t('pbRatio')"></p>
                        <p class="text-lg font-bold"
                           x-text="$store.dashboard.stockData?.data?.stock_info?.pb_ratio"></p>
                    </div>

                    <!-- ROE -->
                    <div class="text-center p-3 bg-dark rounded-lg" x-show="$store.dashboard.stockData?.data?.stock_info?.roe">
                        <p class="text-xs text-gray-400 mb-1" x-text="$store.language.t('roe')"></p>
                        <p class="text-lg font-bold"
                           x-text="$store.dashboard.stockData?.data?.stock_info?.roe + '%'"></p>
                    </div>

                    <!-- Dividend Yield -->
                    <div class="text-center p-3 bg-dark rounded-lg" x-show="$store.dashboard.stockData?.data?.stock_info?.dividend_yield">
                        <p class="text-xs text-gray-400 mb-1" x-text="$store.language.t('dividendYield')"></p>
                        <p class="text-lg font-bold"
                           x-text="$store.dashboard.stockData?.data?.stock_info?.dividend_yield + '%'"></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Empty State -->
        <div x-show="!$store.dashboard.stockData" class="text-center py-8 text-gray-400">
            <p class="text-4xl mb-4">📊</p>
            <p x-text="$store.language.t('searchToStart')"></p>
        </div>
    </div>
</div>
