<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;

class AlphaVantageService
{
    private Client $client;
    private ?string $apiKey;
    private const CACHE_TTL = 86400; // 24 hours for fundamental data

    public function __construct()
    {
        $this->client = new Client([
            'timeout' => 10,
            'verify' => false,
        ]);

        // API key is optional - system works without it
        $this->apiKey = env('ALPHAVANTAGE_API_KEY', null);
    }

    /**
     * Check if Alpha Vantage is configured
     */
    public function isConfigured(): bool
    {
        return $this->apiKey !== null && $this->apiKey !== '';
    }

    /**
     * Enrich stock data with fundamental data from Alpha Vantage
     * This is optional and only runs if API key is configured
     */
    public function enrichFundamentalData(string $symbol, array $stockData): array
    {
        // Skip if not configured
        if (!$this->isConfigured()) {
            return $stockData;
        }

        // Remove .JK, .SI suffixes for Alpha Vantage (they use raw tickers)
        $cleanSymbol = $this->cleanSymbol($symbol);

        // Try to get company overview (has fundamentals)
        $overview = $this->getCompanyOverview($cleanSymbol);

        if ($overview) {
            // Enrich with Alpha Vantage data if available
            $stockData['pe_ratio'] = $stockData['pe_ratio'] ?? ($overview['PERatio'] ?? null);
            $stockData['pb_ratio'] = $stockData['pb_ratio'] ?? ($overview['PriceToBookRatio'] ?? null);
            $stockData['eps'] = $stockData['eps'] ?? ($overview['EPS'] ?? null);
            $stockData['roe'] = $stockData['roe'] ?? ($overview['ReturnOnEquityTTM'] ?? null);
            $stockData['market_cap'] = $stockData['market_cap'] ?? ($overview['MarketCapitalization'] ?? 0);
            $stockData['dividend_yield'] = $stockData['dividend_yield'] ?? ($overview['DividendYield'] ?? null);

            // Convert string values to float
            $stockData['pe_ratio'] = $this->toFloat($stockData['pe_ratio']);
            $stockData['pb_ratio'] = $this->toFloat($stockData['pb_ratio']);
            $stockData['eps'] = $this->toFloat($stockData['eps']);
            $stockData['roe'] = $this->toFloat($stockData['roe']);
            $stockData['dividend_yield'] = $this->toFloat($stockData['dividend_yield']);
            $stockData['market_cap'] = $this->toFloat($stockData['market_cap']);

            // Add metadata
            $stockData['fundamental_source'] = 'alpha_vantage';
        }

        return $stockData;
    }

    /**
     * Get company overview from Alpha Vantage
     */
    private function getCompanyOverview(string $symbol): ?array
    {
        $cacheKey = "alphavantage_overview_{$symbol}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($symbol) {
            try {
                $url = "https://www.alphavantage.co/query?function=OVERVIEW&symbol={$symbol}&apikey={$this->apiKey}";

                $response = $this->client->get($url);
                $data = json_decode($response->getBody()->getContents(), true);

                // Check if we got valid data (not rate limited or error)
                if (isset($data['Symbol']) && $data['Symbol'] === $symbol) {
                    return $data;
                }

                // Log if rate limited
                if (isset($data['Note']) && str_contains($data['Note'], 'rate limit')) {
                    \Log::warning("Alpha Vantage rate limit hit for {$symbol}");
                }

                return null;
            } catch (\Exception $e) {
                \Log::warning("Alpha Vantage error for {$symbol}: " . $e->getMessage());
                return null;
            }
        });
    }

    /**
     * Clean symbol for Alpha Vantage (remove exchange suffixes)
     */
    private function cleanSymbol(string $symbol): string
    {
        // Remove common exchange suffixes
        $symbol = str_replace(['.JK', '.SI', '.KL', '.HK'], '', $symbol);
        return strtoupper($symbol);
    }

    /**
     * Convert string to float safely
     */
    private function toFloat($value): ?float
    {
        if ($value === null || $value === '' || $value === 'None') {
            return null;
        }

        if (is_numeric($value)) {
            return (float) $value;
        }

        return null;
    }

    /**
     * Get rate limit info
     */
    public function getRateLimitInfo(): array
    {
        return [
            'configured' => $this->isConfigured(),
            'free_tier_limit' => '25 requests per day',
            'recommendation' => 'Use for US stocks primarily. IDX/SGX may not have data.',
        ];
    }
}
