import Alpine from 'alpinejs';

/**
 * Stock Chart Component
 * Manages ApexCharts for price and volume display
 */
Alpine.data('stockChart', () => ({
    chart: null,
    loading: false,
    timeframe: '1M', // Default timeframe
    chartType: 'candlestick', // candlestick, line, area

    init() {
        // Initialize chart when component mounts
        this.$watch('$store.dashboard.stockData', () => {
            if (this.$store.dashboard.stockData) {
                this.updateChart();
            }
        });
    },

    updateChart() {
        const stockData = this.$store.dashboard.stockData?.data;
        if (!stockData) return;

        // Destroy existing chart
        if (this.chart) {
            this.chart.destroy();
        }

        // Prepare chart data
        const chartData = this.prepareChartData(stockData);

        // Create new chart
        this.createChart(chartData);
    },

    prepareChartData(stockData) {
        // Get historical data from API
        const ohlcv = stockData.historical_data?.ohlcv || [];
        const volumeArray = stockData.historical_data?.volume || [];

        // Prepare candlestick data
        const candlestickData = [];
        const volumeData = [];

        for (let i = 0; i < ohlcv.length; i++) {
            const item = ohlcv[i];
            if (item.date && item.close) {
                const date = new Date(item.date).getTime();

                // For candlestick (OHLC format)
                candlestickData.push({
                    x: date,
                    y: [item.open, item.high, item.low, item.close]
                });
            }
        }

        for (let i = 0; i < volumeArray.length; i++) {
            const item = volumeArray[i];
            if (item.date && item.volume) {
                const date = new Date(item.date).getTime();
                volumeData.push({
                    x: date,
                    y: item.volume
                });
            }
        }

        return {
            candlestick: candlestickData,
            volume: volumeData,
            symbol: stockData.stock_info?.symbol || '',
            currentPrice: stockData.stock_info?.current_price || 0
        };
    },

    createChart(data) {
        const isDark = true; // Always dark theme

        const options = {
            series: [
                {
                    name: 'Price',
                    type: this.chartType,
                    data: data.candlestick
                },
                {
                    name: 'Volume',
                    type: 'bar',
                    data: data.volume
                }
            ],
            chart: {
                height: 450,
                type: this.chartType,
                background: 'transparent',
                toolbar: {
                    show: true,
                    tools: {
                        download: true,
                        selection: true,
                        zoom: true,
                        zoomin: true,
                        zoomout: true,
                        pan: true,
                        reset: true
                    }
                },
                zoom: {
                    enabled: true
                }
            },
            theme: {
                mode: 'dark'
            },
            title: {
                text: `${data.symbol} Price Chart`,
                align: 'left',
                style: {
                    color: '#e5e7eb'
                }
            },
            xaxis: {
                type: 'datetime',
                labels: {
                    style: {
                        colors: '#9ca3af'
                    }
                }
            },
            yaxis: [
                {
                    seriesName: 'Price',
                    labels: {
                        style: {
                            colors: '#9ca3af'
                        },
                        formatter: (val) => {
                            return val ? val.toLocaleString() : '';
                        }
                    },
                    title: {
                        text: 'Price',
                        style: {
                            color: '#9ca3af'
                        }
                    }
                },
                {
                    seriesName: 'Volume',
                    opposite: true,
                    labels: {
                        style: {
                            colors: '#9ca3af'
                        },
                        formatter: (val) => {
                            if (val >= 1000000) {
                                return (val / 1000000).toFixed(1) + 'M';
                            } else if (val >= 1000) {
                                return (val / 1000).toFixed(1) + 'K';
                            }
                            return val;
                        }
                    },
                    title: {
                        text: 'Volume',
                        style: {
                            color: '#9ca3af'
                        }
                    }
                }
            ],
            plotOptions: {
                candlestick: {
                    colors: {
                        upward: '#10b981', // green
                        downward: '#ef4444'  // red
                    }
                },
                bar: {
                    columnWidth: '80%'
                }
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                width: [1, 1]
            },
            tooltip: {
                theme: 'dark',
                shared: true,
                intersect: false,
                x: {
                    format: 'dd MMM yyyy'
                },
                y: {
                    formatter: (val) => {
                        return val ? val.toLocaleString() : '';
                    }
                }
            },
            grid: {
                borderColor: '#374151',
                strokeDashArray: 3
            },
            legend: {
                show: true,
                position: 'top',
                labels: {
                    colors: '#9ca3af'
                }
            }
        };

        // Create chart
        this.chart = new ApexCharts(this.$refs.chartContainer, options);
        this.chart.render();
    },

    changeTimeframe(timeframe) {
        this.timeframe = timeframe;
        // In a real implementation, this would fetch new data
        // For now, we'll just note the selected timeframe
        console.log('Timeframe changed to:', timeframe);
    },

    changeChartType(type) {
        this.chartType = type;
        this.updateChart();
    },

    destroy() {
        if (this.chart) {
            this.chart.destroy();
        }
    }
}));
