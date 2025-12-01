<?php

namespace App\Http\Controllers;

use App\Services\StockDataFetcher;
use App\Services\StockAnalyzer;
use App\Services\EntryExitAnalyzer;
use App\Services\SwingAnalyzer;
use App\Services\AccumulationDetector;
use App\Services\FavoritesManager;
use App\Models\StockAnalysis;
use App\Data\IndonesianStocks;
use App\Data\SingaporeStocks;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;

class StockAnalysisController extends Controller
{
    private StockDataFetcher $fetcher;
    private StockAnalyzer $analyzer;
    private EntryExitAnalyzer $entryExitAnalyzer;
    private SwingAnalyzer $swingAnalyzer;
    private AccumulationDetector $accumulationDetector;
    private FavoritesManager $favoritesManager;

    public function __construct(
        StockDataFetcher $fetcher,
        StockAnalyzer $analyzer,
        EntryExitAnalyzer $entryExitAnalyzer,
        SwingAnalyzer $swingAnalyzer,
        AccumulationDetector $accumulationDetector,
        FavoritesManager $favoritesManager
    ) {
        $this->fetcher = $fetcher;
        $this->analyzer = $analyzer;
        $this->entryExitAnalyzer = $entryExitAnalyzer;
        $this->swingAnalyzer = $swingAnalyzer;
        $this->accumulationDetector = $accumulationDetector;
        $this->favoritesManager = $favoritesManager;
    }

    /**
     * Normalize stock symbol for Yahoo Finance API
     * Supports Indonesian (.JK), Singapore (.SI), and US stocks
     *
     * @param string $symbol Raw symbol (e.g., "BBCA", "D05", "AAPL", "BBCA.JK")
     * @param string|null $market Optional market identifier ("IDX", "SGX", or "US")
     * @return string Normalized symbol (e.g., "BBCA.JK", "D05.SI", "AAPL")
     */
    private function normalizeSymbol(string $symbol, ?string $market = null): string
    {
        $symbol = strtoupper(trim($symbol));

        // If symbol already has a suffix (contains dot), return as-is
        if (str_contains($symbol, '.')) {
            return $symbol;
        }

        // If market is explicitly specified
        if ($market !== null) {
            $market = strtoupper($market);
            if ($market === 'IDX' || $market === 'ID' || $market === 'INDONESIA') {
                return $symbol . '.JK';
            } elseif ($market === 'SGX' || $market === 'SG' || $market === 'SINGAPORE') {
                return $symbol . '.SI';
            } elseif ($market === 'US' || $market === 'USA' || $market === 'NASDAQ' || $market === 'NYSE') {
                return $symbol; // US stocks don't need suffix
            }
        }

        // Auto-detect based on symbol pattern
        // Indonesian stocks (IDX) are typically 4 characters
        // Singapore stocks (SGX) are typically 3 characters or contain numbers
        // US stocks are typically 1-5 characters

        // Known Singapore blue chips (for better detection)
        $singaporeBlueChips = [
            'D05', 'O39', 'U11', 'Z74', 'C6L', 'BN4', 'U96', 'C52', 'G13', 'S68',
            'J91U', 'M44U', 'ME8U', 'N2IU', 'J85', 'A17U', 'C38U', 'T82U', 'K71U',
            'F34', 'S51', 'Q01', 'F17', 'U14', 'V03', 'AWX', 'U77', 'BSL', 'CC3',
            'S63', 'C09', 'H78', 'L38', '5TG', 'Y92', 'VC2', 'BN2', '5WJ', '1D0',
            'OU8', 'C07', 'M04', 'U10', 'M1'
        ];

        if (in_array($symbol, $singaporeBlueChips)) {
            return $symbol . '.SI';
        }

        // Known Indonesian blue chips (for better detection)
        $indonesianBlueChips = [
            'BBCA', 'BBRI', 'BMRI', 'BBNI', 'TLKM', 'ASII', 'UNVR', 'HMSP',
            'INDF', 'ICBP', 'GGRM', 'KLBF', 'UNTR', 'SMGR', 'PGAS', 'PTBA',
            'ADRO', 'INCO', 'ITMG', 'ANTM', 'WIKA', 'WSKT', 'PWON', 'JSMR',
            'MEDC', 'EXCL', 'SIDO', 'MNCN', 'SCMA', 'CPIN', 'BRPT', 'BSDE'
        ];

        if (in_array($symbol, $indonesianBlueChips)) {
            return $symbol . '.JK';
        }

        // If symbol contains numbers, likely Singapore (e.g., D05, 5TG)
        if (preg_match('/\d/', $symbol)) {
            return $symbol . '.SI';
        }

        // If exactly 4 uppercase letters, likely Indonesian
        if (strlen($symbol) === 4 && ctype_alpha($symbol)) {
            return $symbol . '.JK';
        }

        // If exactly 3 uppercase letters, could be Singapore
        if (strlen($symbol) === 3 && ctype_alpha($symbol)) {
            return $symbol . '.SI';
        }

        // Otherwise assume US stock (1-5 characters, no suffix needed)
        // Common US stocks: AAPL, GOOGL, MSFT, TSLA, AMZN, META, NVDA, etc.
        return $symbol;
    }

    /**
     * Analyze a single stock
     *
     * GET /api/analyze/{symbol}?market=US (optional market parameter)
     */
    public function analyzeSingle(string $symbol, Request $request)
    {
        $market = $request->query('market', null);
        $symbol = $this->normalizeSymbol($symbol, $market);

        $stockData = $this->fetcher->fetchStockData($symbol);

        if (!$stockData) {
            return response()->json([
                'success' => false,
                'message' => "Unable to fetch data for symbol: {$symbol}. Please check if the symbol is correct.",
            ], 404);
        }

        $analysis = $this->analyzer->analyze($stockData);

        return response()->json([
            'success' => true,
            'data' => $analysis,
        ]);
    }

    /**
     * Analyze multiple stocks and rank them
     *
     * POST /api/analyze/multiple
     * Body: { "symbols": ["BBCA", "BBRI", "TLKM"], "market": "US" (optional) }
     */
    public function analyzeMultiple(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'symbols' => 'required|array|min:1|max:20',
            'symbols.*' => 'required|string',
            'market' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 400);
        }

        $market = $request->input('market', null);
        $symbols = array_map(function ($symbol) use ($market) {
            return $this->normalizeSymbol($symbol, $market);
        }, $request->symbols);

        $stocksData = $this->fetcher->fetchMultipleStocks($symbols);

        if (empty($stocksData)) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to fetch data for any of the provided symbols.',
            ], 404);
        }

        $analyses = $this->analyzer->rankStocks($stocksData);

        return response()->json([
            'success' => true,
            'count' => count($analyses),
            'data' => $analyses,
        ]);
    }

    /**
     * Get top Indonesian stocks with analysis
     *
     * GET /api/top-stocks
     */
    public function topStocks()
    {
        // Popular Indonesian stocks (IDX blue chips)
        $symbols = [
            'BBCA.JK',  // Bank Central Asia
            'BBRI.JK',  // Bank Rakyat Indonesia
            'BMRI.JK',  // Bank Mandiri
            'TLKM.JK',  // Telkom Indonesia
            'ASII.JK',  // Astra International
            'UNVR.JK',  // Unilever Indonesia
            'BBNI.JK',  // Bank Negara Indonesia
            'GOTO.JK',  // GoTo Gojek Tokopedia
            'ICBP.JK',  // Indofood CBP
            'INDF.JK',  // Indofood Sukses Makmur
        ];

        $stocksData = $this->fetcher->fetchMultipleStocks($symbols);
        $analyses = $this->analyzer->rankStocks($stocksData);

        return response()->json([
            'success' => true,
            'count' => count($analyses),
            'data' => $analyses,
            'updated_at' => now()->toDateTimeString(),
        ]);
    }

    /**
     * Get stock data only (without analysis)
     *
     * GET /api/stock/{symbol}
     */
    public function getStock(string $symbol)
    {
        if (!str_ends_with($symbol, '.JK')) {
            $symbol = strtoupper($symbol) . '.JK';
        }

        $stockData = $this->fetcher->fetchStockData($symbol);

        if (!$stockData) {
            return response()->json([
                'success' => false,
                'message' => "Unable to fetch data for symbol: {$symbol}",
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $stockData,
        ]);
    }

    /**
     * Compare multiple stocks side by side
     *
     * POST /api/compare
     * Body: { "symbols": ["BBCA", "BBRI"] }
     */
    public function compareStocks(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'symbols' => 'required|array|min:2|max:5',
            'symbols.*' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 400);
        }

        $symbols = array_map(function ($symbol) {
            return str_ends_with($symbol, '.JK') ? strtoupper($symbol) : strtoupper($symbol) . '.JK';
        }, $request->symbols);

        $stocksData = $this->fetcher->fetchMultipleStocks($symbols);

        if (count($stocksData) < 2) {
            return response()->json([
                'success' => false,
                'message' => 'Need at least 2 valid stocks to compare.',
            ], 400);
        }

        $comparison = [];
        foreach ($stocksData as $data) {
            $analysis = $this->analyzer->analyze($data);
            $comparison[] = [
                'symbol' => $data['symbol'],
                'name' => $data['name'],
                'price' => $data['current_price'],
                'change_percent' => round($data['change_percent'], 2),
                'score' => $analysis['score'],
                'recommendation' => $analysis['recommendation']['action'],
                'pe_ratio' => $data['pe_ratio'],
                'pb_ratio' => $data['pb_ratio'],
                'dividend_yield' => $data['dividend_yield'] ? round($data['dividend_yield'] * 100, 2) : 0,
                'roe' => $data['roe'] ? round($data['roe'] * 100, 2) : null,
                'market_cap' => $data['market_cap'],
            ];
        }

        // Sort by score
        usort($comparison, function ($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        return response()->json([
            'success' => true,
            'data' => $comparison,
        ]);
    }

    /**
     * Display web interface
     *
     * GET /
     */
    public function index()
    {
        return view('stock-analyzer');
    }

    /**
     * Clear cache for a symbol
     *
     * DELETE /api/cache/{symbol}
     */
    public function clearCache(string $symbol)
    {
        if (!str_ends_with($symbol, '.JK')) {
            $symbol = strtoupper($symbol) . '.JK';
        }

        $this->fetcher->clearCache($symbol);

        return response()->json([
            'success' => true,
            'message' => "Cache cleared for {$symbol}",
        ]);
    }

    /**
     * Get comprehensive dashboard data for a stock
     *
     * GET /api/dashboard/{symbol}?market=US (optional market parameter)
     */
    public function getDashboard(string $symbol, Request $request)
    {
        $market = $request->query('market', null);
        $symbol = $this->normalizeSymbol($symbol, $market);

        // Cache dashboard results for 10 minutes (600 seconds)
        $cacheKey = "dashboard_{$symbol}";

        return Cache::remember($cacheKey, 600, function () use ($symbol, $market) {
            $stockData = $this->fetcher->fetchStockData($symbol);

            if (!$stockData) {
                return response()->json([
                    'success' => false,
                    'message' => "Unable to fetch data for symbol: {$symbol}",
                ], 404);
            }

            // Get all analyses
            $analysis = $this->analyzer->analyze($stockData);
            $entryExit = $this->entryExitAnalyzer->analyze($stockData);
            $swing = $this->swingAnalyzer->analyze($stockData);
            $accumulation = $this->accumulationDetector->analyze($stockData);
            $isFavorite = $this->favoritesManager->isFavorite($symbol);

            // Save analysis to database for ML training (async - non-blocking)
            dispatch(function () use ($symbol, $market, $stockData, $analysis, $swing, $accumulation) {
                $this->saveAnalysisForML($symbol, $market, $stockData, $analysis, $swing, $accumulation);
            })->afterResponse();

            return response()->json([
                'success' => true,
                'data' => [
                    'stock_info' => [
                        'symbol' => $stockData['symbol'],
                        'name' => $stockData['name'],
                        'current_price' => $stockData['current_price'],
                        'change' => $stockData['change'],
                        'change_percent' => $stockData['change_percent'],
                        'volume' => $stockData['volume'],
                        'market_cap' => $stockData['market_cap'],
                    ],
                    'overall_analysis' => $analysis,
                    'entry_exit' => $entryExit,
                    'swing_analysis' => $swing,
                    'accumulation' => $accumulation,
                    'is_favorite' => $isFavorite,
                    'updated_at' => now()->toDateTimeString(),
                    'cached' => false,
                ],
            ]);
        });
    }

    /**
     * Display comprehensive dashboard view
     *
     * GET /dashboard
     */
    public function dashboard()
    {
        return view('dashboard');
    }

    /**
     * Get favorites list
     *
     * GET /api/favorites
     */
    public function getFavorites()
    {
        $favorites = $this->favoritesManager->getFavorites();

        return response()->json([
            'success' => true,
            'count' => count($favorites),
            'data' => $favorites,
        ]);
    }

    /**
     * Add to favorites
     *
     * POST /api/favorites
     * Body: { "symbol": "BBCA", "name": "Bank Central Asia" }
     */
    public function addFavorite(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'symbol' => 'required|string',
            'name' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 400);
        }

        $added = $this->favoritesManager->addFavorite(
            $request->symbol,
            $request->name
        );

        if (!$added) {
            return response()->json([
                'success' => false,
                'message' => 'Stock already in favorites',
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Stock added to favorites',
        ]);
    }

    /**
     * Remove from favorites
     *
     * DELETE /api/favorites/{symbol}
     */
    public function removeFavorite(string $symbol)
    {
        $removed = $this->favoritesManager->removeFavorite($symbol);

        return response()->json([
            'success' => true,
            'message' => $removed ? 'Stock removed from favorites' : 'Stock not in favorites',
        ]);
    }

    /**
     * Get dashboard data for multiple favorites
     *
     * GET /api/favorites/dashboard
     */
    public function favoritesDashboard()
    {
        $symbols = $this->favoritesManager->getFavoriteSymbols();

        if (empty($symbols)) {
            return response()->json([
                'success' => true,
                'message' => 'No favorites added yet',
                'data' => [],
            ]);
        }

        // Cache based on favorite symbols list (10 minutes)
        $cacheKey = "favorites_dashboard_" . md5(json_encode($symbols));

        return Cache::remember($cacheKey, 600, function () use ($symbols) {
            $stocksData = $this->fetcher->fetchMultipleStocks($symbols);
            $dashboards = [];

            foreach ($stocksData as $stockData) {
                $analysis = $this->analyzer->analyze($stockData);
                $entryExit = $this->entryExitAnalyzer->analyze($stockData);
                $accumulation = $this->accumulationDetector->analyze($stockData);
                $swing = $this->swingAnalyzer->analyze($stockData);

                $dashboards[] = [
                    'symbol' => $stockData['symbol'],
                    'name' => $stockData['name'],
                    'price' => $stockData['current_price'],
                    'change_percent' => round($stockData['change_percent'], 2),
                    'score' => $analysis['score'],
                    'recommendation' => $analysis['recommendation']['action'],
                    'accumulation_phase' => $accumulation['phase']['current_phase'],
                    'entry_action' => $entryExit['position_recommendation']['recommended_action'],
                    'swing_rating' => $swing['swing_rating']['rating'],
                ];
            }

            // Sort by score
            usort($dashboards, function ($a, $b) {
                return $b['score'] <=> $a['score'];
            });

            return response()->json([
                'success' => true,
                'count' => count($dashboards),
                'data' => $dashboards,
                'cached' => false,
            ]);
        });
    }

    /**
     * Save analysis data for ML training
     */
    private function saveAnalysisForML(
        string $symbol,
        ?string $market,
        array $stockData,
        array $analysis,
        array $swing,
        array $accumulation
    ): void {
        try {
            StockAnalysis::create([
                'symbol' => $symbol,
                'market' => $market ?? 'idx',
                'price' => $stockData['current_price'],

                // Technical indicators
                'rsi' => $analysis['metrics']['technical']['rsi'] ?? null,
                'above_sma' => $analysis['metrics']['technical']['above_sma'] ?? null,
                'volume_ratio' => $accumulation['current_volume_vs_average']['ratio'] ?? null,
                'volatility_rating' => $swing['volatility']['volatility_rating'] ?? null,

                // Fundamentals
                'pe_ratio' => $analysis['metrics']['valuation']['pe_ratio'] ?? null,
                'pb_ratio' => $analysis['metrics']['valuation']['pb_ratio'] ?? null,
                'roe' => $analysis['metrics']['profitability']['roe'] ?? null,
                'eps' => $analysis['metrics']['profitability']['eps'] ?? null,

                // Accumulation
                'accumulation_phase' => $accumulation['phase']['current_phase'] ?? null,
                'accumulation_strength' => $accumulation['strength']['score'] ?? null,
                'accumulation_days' => $accumulation['duration']['days'] ?? null,
                'participant_type' => $accumulation['participants']['primary_type'] ?? null,
                'institutional_percent' => $accumulation['participants']['institutional_percent'] ?? null,

                // Swing
                'swing_score' => $swing['swing_rating']['score'] ?? null,
                'avg_swing_percent' => $swing['swing_size']['average_swing_percent'] ?? null,
                'trend_pattern' => $swing['swing_pattern']['pattern'] ?? null,

                // Overall
                'overall_score' => $analysis['score'],
                'recommendation' => $analysis['recommendation']['action'],
            ]);
        } catch (\Exception $e) {
            // Log error but don't fail the request
            \Log::error('Failed to save analysis for ML: ' . $e->getMessage());
        }
    }

    /**
     * Scan stocks for buy opportunities with 3-hour caching
     *
     * GET /api/scan-opportunities?market=idx&refresh=true (optional)
     */
    public function scanOpportunities(Request $request)
    {
        $market = $request->query('market', 'auto');
        $forceRefresh = $request->query('refresh', false);

        // Cache key based on market selection
        $cacheKey = "buy_opportunities_{$market}";

        // If force refresh requested, clear cache
        if ($forceRefresh) {
            Cache::forget($cacheKey);
        }

        // Cache results for 3 hours (180 minutes)
        $result = Cache::remember($cacheKey, 180 * 60, function () use ($market) {
            return $this->performOpportunitiesScan($market);
        });

        // Add cache metadata
        $result['cached_at'] = Cache::get($cacheKey . '_timestamp', now()->toDateTimeString());
        $result['cache_expires_in_minutes'] = 180;

        return response()->json($result);
    }

    /**
     * Scan for stocks with institutional accumulation
     *
     * GET /api/scan-institutional-stocks?market=idx&refresh=true (optional)
     */
    public function scanInstitutionalStocks(Request $request)
    {
        $market = $request->query('market', 'idx');
        $minInstitutionalPercent = $request->query('min_institutional', 60); // Default 60%
        $forceRefresh = $request->query('refresh', false);

        // Cache key based on market and threshold
        $cacheKey = "scan_institutional_{$market}_{$minInstitutionalPercent}";

        // If force refresh requested, clear cache
        if ($forceRefresh) {
            Cache::forget($cacheKey);
        }

        // Cache results for 1 hour (3600 seconds)
        return Cache::remember($cacheKey, 3600, function () use ($market, $minInstitutionalPercent) {
            // Get all stocks based on market
            $allStocks = [];
            if ($market === 'idx') {
                // Use top 50 big cap (institutions prefer liquid, large cap stocks)
                $allStocks = IndonesianStocks::getTopBigCap();
            } elseif ($market === 'sgx') {
                // Use Singapore big cap stocks
                $allStocks = SingaporeStocks::getTopBigCap();
            } elseif ($market === 'us') {
                $allStocks = ['AAPL', 'MSFT', 'GOOGL', 'AMZN', 'NVDA', 'TSLA', 'AMD', 'META'];
            }

            $institutionalStocks = [];
            $scanned = 0;
            $errors = 0;

            foreach ($allStocks as $symbol) {
                try {
                    $normalizedSymbol = $this->normalizeSymbol($symbol, $market);
                    $stockData = $this->fetcher->fetchStockData($normalizedSymbol);

                    if (!$stockData) {
                        $errors++;
                        continue;
                    }

                    $analysis = $this->analyzer->analyze($stockData);
                    $accumulation = $this->accumulationDetector->analyze($stockData);
                    $swing = $this->swingAnalyzer->analyze($stockData);

                    $scanned++;

                    $institutionalPercent = $accumulation['participants']['institutional_percent'] ?? 0;
                    $participantType = $accumulation['participants']['primary_type'] ?? 'Unknown';

                    // Filter for institutional stocks
                    if ($institutionalPercent >= $minInstitutionalPercent || $participantType === 'Institutional') {
                        $institutionalStocks[] = [
                            'symbol' => $symbol,
                            'name' => $stockData['name'],
                            'price' => $stockData['current_price'],
                            'change_percent' => $stockData['change_percent'],
                            'score' => $analysis['score'],
                            'action' => $analysis['recommendation']['action'],

                            // Institutional indicators
                            'institutional_percent' => $institutionalPercent,
                            'participant_type' => $participantType,
                            'accumulation_phase' => $accumulation['phase']['current_phase'] ?? 'N/A',
                            'accumulation_strength' => $accumulation['strength']['score'] ?? 0,
                            'accumulation_days' => $accumulation['duration']['days'] ?? 0,

                            // Volume patterns (institutional signature)
                            'volume_pattern' => $this->getVolumePattern($accumulation),
                            'price_stability' => $swing['volatility']['volatility_rating'] ?? 'N/A',

                            // Market data
                            'market_cap' => $stockData['market_cap'],
                            'volume' => $stockData['volume'],
                            'avg_volume' => $stockData['avg_volume'],
                        ];
                    }
                } catch (\Exception $e) {
                    $errors++;
                    \Log::warning("Failed to scan {$symbol}: " . $e->getMessage());
                    continue;
                }
            }

            // Sort by institutional percentage (highest first)
            usort($institutionalStocks, fn($a, $b) => $b['institutional_percent'] <=> $a['institutional_percent']);

            return response()->json([
                'success' => true,
                'scanned' => $scanned,
                'errors' => $errors,
                'total_stocks' => count($allStocks),
                'institutional_stocks_found' => count($institutionalStocks),
                'min_institutional_threshold' => $minInstitutionalPercent,
                'data' => $institutionalStocks,
                'top_10' => array_slice($institutionalStocks, 0, 10),
                'cached' => false,
                'cached_at' => now()->toDateTimeString(),
            ]);
        });
    }

    /**
     * Get volume pattern description from accumulation data
     */
    private function getVolumePattern(array $accumulation): string
    {
        $indicators = $accumulation['participants']['indicators'] ?? [];

        if (empty($indicators)) {
            return 'Normal';
        }

        // Look for institutional patterns
        foreach ($indicators as $indicator) {
            if (stripos($indicator, 'institutional') !== false) {
                if (stripos($indicator, 'Consistent volume') !== false) {
                    return 'Consistent (Institutional)';
                }
                if (stripos($indicator, 'high volume') !== false) {
                    return 'High Volume, Low Volatility';
                }
                if (stripos($indicator, 'systematic') !== false) {
                    return 'Systematic Accumulation';
                }
            }
        }

        return 'Mixed';
    }

    /**
     * Scan ALL stocks comprehensively (slower but complete)
     *
     * GET /api/scan-all-stocks?market=idx&refresh=true (optional)
     */
    public function scanAllStocks(Request $request)
    {
        $market = $request->query('market', 'idx');
        $forceRefresh = $request->query('refresh', false);

        // Cache key based on market
        $cacheKey = "scan_all_stocks_{$market}";

        // If force refresh requested, clear cache
        if ($forceRefresh) {
            Cache::forget($cacheKey);
        }

        // Cache results for 1 hour (3600 seconds)
        return Cache::remember($cacheKey, 3600, function () use ($market) {
            // Get all stocks based on market
            $allStocks = [];
            if ($market === 'idx') {
                $allStocks = IndonesianStocks::getAll();
            } elseif ($market === 'us') {
                // Could add US comprehensive list here
                $allStocks = ['AAPL', 'MSFT', 'GOOGL', 'AMZN', 'NVDA', 'TSLA', 'AMD', 'META'];
            }

            $buyOpportunities = [];
            $scalpingOpportunities = [];
            $scanned = 0;
            $errors = 0;

            foreach ($allStocks as $symbol) {
                try {
                    $normalizedSymbol = $this->normalizeSymbol($symbol, $market);
                    $stockData = $this->fetcher->fetchStockData($normalizedSymbol);

                    if (!$stockData) {
                        $errors++;
                        continue;
                    }

                    $analysis = $this->analyzer->analyze($stockData);
                    $accumulation = $this->accumulationDetector->analyze($stockData);
                    $swing = $this->swingAnalyzer->analyze($stockData);

                    $scanned++;

                    $action = $analysis['recommendation']['action'];
                    $stockInfo = [
                        'symbol' => $symbol,
                        'name' => $stockData['name'],
                        'price' => $stockData['current_price'],
                        'score' => $analysis['score'],
                        'action' => $action,
                        'volatility' => $swing['volatility']['volatility_percent'] ?? 0,
                        'swing_score' => $swing['swing_rating']['score'] ?? 0,
                    ];

                    // Categorize as BUY opportunity
                    if (strpos($action, 'BUY') !== false) {
                        $buyOpportunities[] = $stockInfo;
                    }

                    // Categorize as SCALPING opportunity (high volatility)
                    if (($stockInfo['volatility'] > 15 || $stockInfo['swing_score'] > 60) &&
                        $stockData['volume'] > 1000000) {
                        $scalpingOpportunities[] = $stockInfo;
                    }
                } catch (\Exception $e) {
                    $errors++;
                    \Log::warning("Failed to scan {$symbol}: " . $e->getMessage());
                    continue;
                }
            }

            // Sort by score
            usort($buyOpportunities, fn($a, $b) => $b['score'] <=> $a['score']);
            usort($scalpingOpportunities, fn($a, $b) => $b['volatility'] <=> $a['volatility']);

            return response()->json([
                'success' => true,
                'scanned' => $scanned,
                'errors' => $errors,
                'total_stocks' => count($allStocks),
                'top_10_buy' => array_slice($buyOpportunities, 0, 10),
                'top_5_scalping' => array_slice($scalpingOpportunities, 0, 5),
                'all_buy_opportunities' => $buyOpportunities,
                'all_scalping_opportunities' => $scalpingOpportunities,
                'cached' => false,
                'cached_at' => now()->toDateTimeString(),
            ]);
        });
    }

    /**
     * Perform the actual stock scanning (uses expanded 50-stock lists)
     */
    private function performOpportunitiesScan(string $market): array
    {
        $stocksToScan = [];

        if ($market === 'idx' || $market === 'auto') {
            // Use expanded list with 50 Indonesian stocks
            $stocksToScan = array_merge($stocksToScan, IndonesianStocks::getExpandedScannerList());
        }

        if ($market === 'sgx' || $market === 'auto') {
            // Use expanded list with 50 Singapore stocks
            $stocksToScan = array_merge($stocksToScan, SingaporeStocks::getExpandedScannerList());
        }

        if ($market === 'us' || $market === 'auto') {
            // Expanded US stocks list (50 stocks)
            $stocksToScan = array_merge($stocksToScan, [
                // Top 10 Big Tech
                'AAPL', 'MSFT', 'GOOGL', 'AMZN', 'NVDA', 'META', 'TSLA', 'NFLX', 'AMD', 'INTC',
                // Top 10 Finance & Healthcare
                'JPM', 'BAC', 'WFC', 'GS', 'MS', 'JNJ', 'UNH', 'PFE', 'ABBV', 'TMO',
                // Top 10 Consumer & Retail
                'WMT', 'HD', 'NKE', 'SBUX', 'MCD', 'DIS', 'COST', 'TGT', 'LOW', 'TJX',
                // Top 10 Industrial & Energy
                'BA', 'CAT', 'GE', 'MMM', 'HON', 'XOM', 'CVX', 'COP', 'SLB', 'EOG',
                // Top 10 High-Growth Tech
                'COIN', 'PLTR', 'SNOW', 'CRWD', 'ZS', 'NET', 'DDOG', 'MDB', 'U', 'RBLX',
            ]);
        }

        $opportunities = [];
        $nearMisses = [];
        $scanned = 0;
        $errors = 0;

        foreach ($stocksToScan as $symbol) {
            try {
                // Determine market for normalization
                $marketHint = null;
                if ($market === 'idx') {
                    $marketHint = 'idx';
                } elseif ($market === 'sgx') {
                    $marketHint = 'sgx';
                } elseif ($market === 'us') {
                    $marketHint = 'us';
                }

                $normalizedSymbol = $this->normalizeSymbol($symbol, $marketHint);
                $stockData = $this->fetcher->fetchStockData($normalizedSymbol);

                if (!$stockData) {
                    $errors++;
                    continue;
                }

                $analysis = $this->analyzer->analyze($stockData);
                $accumulation = $this->accumulationDetector->analyze($stockData);
                $swing = $this->swingAnalyzer->analyze($stockData);

                $scanned++;

                $action = $analysis['recommendation']['action'];
                $score = $analysis['score'];

                // Determine market from symbol
                $detectedMarket = 'us';
                if (strpos($normalizedSymbol, '.JK') !== false) {
                    $detectedMarket = 'idx';
                } elseif (strpos($normalizedSymbol, '.SI') !== false) {
                    $detectedMarket = 'sgx';
                }

                $stockInfo = [
                    'symbol' => $symbol,
                    'normalized_symbol' => $normalizedSymbol,
                    'name' => $stockData['name'],
                    'price' => $stockData['current_price'],
                    'change_percent' => $stockData['change_percent'],
                    'action' => $action,
                    'score' => $score,
                    'confidence' => $analysis['recommendation']['confidence'],
                    'market' => $detectedMarket,

                    // Key signals
                    'macd_signal' => $analysis['metrics']['technical']['macd']['signal'] ?? 'N/A',
                    'divergence' => $analysis['metrics']['technical']['divergence']['divergence'] ?? 'NONE',
                    'week52_position' => $analysis['metrics']['technical']['52_week']['position'] ?? 'N/A',
                    'rsi' => $analysis['metrics']['technical']['rsi'] ?? null,
                    'institutional_percent' => $accumulation['participants']['institutional_percent'] ?? 0,
                    'accumulation_phase' => $accumulation['phase']['current_phase'] ?? 'N/A',
                ];

                // Categorize: BUY opportunities
                if (strpos($action, 'BUY') !== false) {
                    $opportunities[] = $stockInfo;
                }
                // Near-miss: High scores but not quite BUY (60-74 score range, or HOLD with bullish signals)
                elseif ($score >= 60 && $score < 75) {
                    $stockInfo['reason'] = 'High score (' . $score . ') but ' . $action;
                    $nearMisses[] = $stockInfo;
                }
                // Also track HOLD with strong technical signals
                elseif (strpos($action, 'HOLD') !== false && $score >= 55) {
                    $stockInfo['reason'] = 'Hold recommendation but decent score (' . $score . ')';
                    $nearMisses[] = $stockInfo;
                }
            } catch (\Exception $e) {
                $errors++;
                \Log::warning("Failed to scan {$symbol}: " . $e->getMessage());
                continue;
            }
        }

        // Sort by score descending
        usort($opportunities, function ($a, $b) {
            return $b['score'] <=> $a['score'];
        });
        usort($nearMisses, function ($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        // Store timestamp
        Cache::put('buy_opportunities_' . $market . '_timestamp', now()->toDateTimeString(), 180 * 60);

        return [
            'success' => true,
            'scanned' => $scanned,
            'errors' => $errors,
            'total_stocks' => count($stocksToScan),
            'opportunities_found' => count($opportunities),
            'near_misses_found' => count($nearMisses),
            'data' => array_slice($opportunities, 0, 10), // Top 10 BUY opportunities
            'all_opportunities' => $opportunities, // All BUY opportunities
            'near_misses' => array_slice($nearMisses, 0, 10), // Top 10 near-misses
            'all_near_misses' => $nearMisses, // All near-misses
        ];
    }
}
