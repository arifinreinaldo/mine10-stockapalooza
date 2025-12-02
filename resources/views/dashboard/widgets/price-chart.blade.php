<!-- Price Chart Widget -->
<div class="card"
     x-data="stockChart"
     x-init="init()"
     x-show="$store.dashboard.stockData">
    <div class="widget-header">
        <div class="flex items-center justify-between w-full">
            <div class="flex items-center gap-2">
                <span class="text-2xl">📈</span>
                <h3 class="text-lg font-semibold" x-text="$store.language.t('priceChart')"></h3>
            </div>

            <!-- Chart Controls -->
            <div class="flex items-center gap-3">
                <!-- Chart Type Selector -->
                <div class="flex gap-1 bg-dark rounded-lg p-1">
                    <button @click="changeChartType('candlestick')"
                            class="px-3 py-1 rounded text-xs transition-colors"
                            :class="chartType === 'candlestick' ? 'bg-primary-500 text-white' : 'text-gray-400 hover:text-white'">
                        Candle
                    </button>
                    <button @click="changeChartType('line')"
                            class="px-3 py-1 rounded text-xs transition-colors"
                            :class="chartType === 'line' ? 'bg-primary-500 text-white' : 'text-gray-400 hover:text-white'">
                        Line
                    </button>
                    <button @click="changeChartType('area')"
                            class="px-3 py-1 rounded text-xs transition-colors"
                            :class="chartType === 'area' ? 'bg-primary-500 text-white' : 'text-gray-400 hover:text-white'">
                        Area
                    </button>
                </div>

                <!-- Timeframe Selector -->
                <div class="flex gap-1 bg-dark rounded-lg p-1">
                    <button @click="changeTimeframe('1D')"
                            class="px-2 py-1 rounded text-xs transition-colors"
                            :class="timeframe === '1D' ? 'bg-primary-500 text-white' : 'text-gray-400 hover:text-white'">
                        1D
                    </button>
                    <button @click="changeTimeframe('1W')"
                            class="px-2 py-1 rounded text-xs transition-colors"
                            :class="timeframe === '1W' ? 'bg-primary-500 text-white' : 'text-gray-400 hover:text-white'">
                        1W
                    </button>
                    <button @click="changeTimeframe('1M')"
                            class="px-2 py-1 rounded text-xs transition-colors"
                            :class="timeframe === '1M' ? 'bg-primary-500 text-white' : 'text-gray-400 hover:text-white'">
                        1M
                    </button>
                    <button @click="changeTimeframe('3M')"
                            class="px-2 py-1 rounded text-xs transition-colors"
                            :class="timeframe === '3M' ? 'bg-primary-500 text-white' : 'text-gray-400 hover:text-white'">
                        3M
                    </button>
                    <button @click="changeTimeframe('6M')"
                            class="px-2 py-1 rounded text-xs transition-colors"
                            :class="timeframe === '6M' ? 'bg-primary-500 text-white' : 'text-gray-400 hover:text-white'">
                        6M
                    </button>
                    <button @click="changeTimeframe('1Y')"
                            class="px-2 py-1 rounded text-xs transition-colors"
                            :class="timeframe === '1Y' ? 'bg-primary-500 text-white' : 'text-gray-400 hover:text-white'">
                        1Y
                    </button>
                    <button @click="changeTimeframe('ALL')"
                            class="px-2 py-1 rounded text-xs transition-colors"
                            :class="timeframe === 'ALL' ? 'bg-primary-500 text-white' : 'text-gray-400 hover:text-white'">
                        ALL
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart Container -->
    <div class="relative">
        <!-- Loading Overlay -->
        <div x-show="loading"
             class="absolute inset-0 bg-dark/80 flex items-center justify-center z-10 rounded-lg">
            <div class="spinner border-primary-500"></div>
        </div>

        <!-- ApexCharts Container -->
        <div x-ref="chartContainer" class="w-full"></div>
    </div>

    <!-- Chart Info -->
    <div class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-3 text-sm">
        <div class="text-center p-3 bg-dark rounded-lg"
             x-show="$store.dashboard.stockData?.data?.stock_info?.current_price">
            <p class="text-xs text-gray-400 mb-1" x-text="$store.language.t('currentPrice')"></p>
            <p class="font-bold"
               x-text="$store.dashboard.stockData?.data?.stock_info?.current_price?.toLocaleString()"></p>
        </div>

        <div class="text-center p-3 bg-dark rounded-lg"
             x-show="$store.dashboard.stockData?.data?.stock_info?.change_percent !== undefined">
            <p class="text-xs text-gray-400 mb-1" x-text="$store.language.t('change')"></p>
            <p class="font-bold"
               :class="$store.dashboard.stockData?.data?.stock_info?.change_percent >= 0 ? 'text-success' : 'text-danger'"
               x-text="($store.dashboard.stockData?.data?.stock_info?.change_percent >= 0 ? '+' : '') +
                       $store.dashboard.stockData?.data?.stock_info?.change_percent?.toFixed(2) + '%'"></p>
        </div>

        <div class="text-center p-3 bg-dark rounded-lg"
             x-show="$store.dashboard.stockData?.data?.stock_info?.volume">
            <p class="text-xs text-gray-400 mb-1" x-text="$store.language.t('volume')"></p>
            <p class="font-bold"
               x-text="($store.dashboard.stockData?.data?.stock_info?.volume / 1000000).toFixed(2) + 'M'"></p>
        </div>

        <div class="text-center p-3 bg-dark rounded-lg"
             x-show="$store.dashboard.stockData?.data?.overall_analysis?.metrics?.technical?.['52_week']?.high_52w">
            <p class="text-xs text-gray-400 mb-1">52W High</p>
            <p class="font-bold"
               x-text="$store.dashboard.stockData?.data?.overall_analysis?.metrics?.technical?.['52_week']?.high_52w?.toLocaleString()"></p>
        </div>
    </div>
</div>
