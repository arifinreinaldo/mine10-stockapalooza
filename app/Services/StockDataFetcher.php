<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;

class StockDataFetcher
{
    private Client $client;
    private const CACHE_TTL = 300; // 5 minutes

    public function __construct()
    {
        $this->client = new Client([
            'timeout' => 10,
            'verify' => false,
        ]);
    }

    /**
     * Fetch stock data from Yahoo Finance API
     *
     * @param string $symbol Stock symbol (e.g., 'BBCA.JK')
     * @return array|null
     */
    public function fetchStockData(string $symbol): ?array
    {
        $cacheKey = "stock_data_{$symbol}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($symbol) {
            try {
                // Yahoo Finance API endpoints
                $quoteUrl = "https://query1.finance.yahoo.com/v8/finance/chart/{$symbol}";
                $statsUrl = "https://query2.finance.yahoo.com/v10/finance/quoteSummary/{$symbol}?modules=defaultKeyStatistics,financialData,summaryDetail";

                // Fetch quote data (price, volume, etc.)
                $quoteResponse = $this->client->get($quoteUrl);
                $quoteData = json_decode($quoteResponse->getBody()->getContents(), true);

                // Fetch statistics (P/E, P/B, etc.)
                $statsResponse = $this->client->get($statsUrl);
                $statsData = json_decode($statsResponse->getBody()->getContents(), true);

                if (!isset($quoteData['chart']['result'][0])) {
                    return null;
                }

                $result = $quoteData['chart']['result'][0];
                $meta = $result['meta'];
                $indicators = $result['indicators']['quote'][0] ?? [];

                $stats = $statsData['quoteSummary']['result'][0] ?? [];
                $keyStats = $stats['defaultKeyStatistics'] ?? [];
                $financialData = $stats['financialData'] ?? [];
                $summaryDetail = $stats['summaryDetail'] ?? [];

                // Calculate technical indicators
                $closes = $indicators['close'] ?? [];
                $volumes = $indicators['volume'] ?? [];

                return [
                    'symbol' => $symbol,
                    'name' => $meta['longName'] ?? $meta['shortName'] ?? $symbol,
                    'currency' => $meta['currency'] ?? 'IDR',
                    'exchange' => $meta['exchangeName'] ?? 'JKT',

                    // Price data
                    'current_price' => $meta['regularMarketPrice'] ?? 0,
                    'previous_close' => $meta['previousClose'] ?? 0,
                    'open' => $meta['regularMarketOpen'] ?? 0,
                    'day_high' => $meta['regularMarketDayHigh'] ?? 0,
                    'day_low' => $meta['regularMarketDayLow'] ?? 0,
                    'change' => ($meta['regularMarketPrice'] ?? 0) - ($meta['previousClose'] ?? 1),
                    'change_percent' => ((($meta['regularMarketPrice'] ?? 0) - ($meta['previousClose'] ?? 1)) / ($meta['previousClose'] ?? 1)) * 100,

                    // Volume
                    'volume' => $meta['regularMarketVolume'] ?? 0,
                    'avg_volume' => $summaryDetail['averageVolume']['raw'] ?? 0,

                    // Market cap
                    'market_cap' => $summaryDetail['marketCap']['raw'] ?? 0,

                    // Fundamental ratios
                    'pe_ratio' => $summaryDetail['trailingPE']['raw'] ?? null,
                    'forward_pe' => $summaryDetail['forwardPE']['raw'] ?? null,
                    'pb_ratio' => $keyStats['priceToBook']['raw'] ?? null,
                    'eps' => $keyStats['trailingEps']['raw'] ?? null,
                    'dividend_yield' => $summaryDetail['dividendYield']['raw'] ?? 0,
                    'dividend_rate' => $summaryDetail['dividendRate']['raw'] ?? 0,
                    'beta' => $keyStats['beta']['raw'] ?? null,

                    // Financial health
                    'profit_margin' => $financialData['profitMargins']['raw'] ?? null,
                    'operating_margin' => $financialData['operatingMargins']['raw'] ?? null,
                    'roe' => $financialData['returnOnEquity']['raw'] ?? null,
                    'roa' => $financialData['returnOnAssets']['raw'] ?? null,
                    'debt_to_equity' => $financialData['debtToEquity']['raw'] ?? null,
                    'current_ratio' => $financialData['currentRatio']['raw'] ?? null,

                    // Recommendations
                    'recommendation' => $financialData['recommendationKey'] ?? 'none',
                    'target_price' => $financialData['targetMeanPrice']['raw'] ?? null,

                    // Historical data for technical analysis
                    'historical_closes' => array_slice($closes, -50), // Last 50 days
                    'historical_volumes' => array_slice($volumes, -50),

                    'fetched_at' => now()->toDateTimeString(),
                ];

            } catch (\Exception $e) {
                \Log::error("Error fetching stock data for {$symbol}: " . $e->getMessage());
                return null;
            }
        });
    }

    /**
     * Fetch multiple stocks data
     *
     * @param array $symbols
     * @return array
     */
    public function fetchMultipleStocks(array $symbols): array
    {
        $results = [];

        foreach ($symbols as $symbol) {
            $data = $this->fetchStockData($symbol);
            if ($data) {
                $results[] = $data;
            }
        }

        return $results;
    }

    /**
     * Clear cache for a symbol
     */
    public function clearCache(string $symbol): void
    {
        Cache::forget("stock_data_{$symbol}");
    }
}
