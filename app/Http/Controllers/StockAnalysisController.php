<?php

namespace App\Http\Controllers;

use App\Services\StockDataFetcher;
use App\Services\StockAnalyzer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StockAnalysisController extends Controller
{
    private StockDataFetcher $fetcher;
    private StockAnalyzer $analyzer;

    public function __construct(StockDataFetcher $fetcher, StockAnalyzer $analyzer)
    {
        $this->fetcher = $fetcher;
        $this->analyzer = $analyzer;
    }

    /**
     * Analyze a single stock
     *
     * GET /api/analyze/{symbol}
     */
    public function analyzeSingle(string $symbol)
    {
        // Add .JK suffix if not present (for Indonesian stocks)
        if (!str_ends_with($symbol, '.JK')) {
            $symbol = strtoupper($symbol) . '.JK';
        }

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
     * Body: { "symbols": ["BBCA", "BBRI", "TLKM"] }
     */
    public function analyzeMultiple(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'symbols' => 'required|array|min:1|max:20',
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
}
