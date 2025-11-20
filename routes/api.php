<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StockAnalysisController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Analyze single stock
Route::get('/analyze/{symbol}', [StockAnalysisController::class, 'analyzeSingle']);

// Analyze multiple stocks
Route::post('/analyze/multiple', [StockAnalysisController::class, 'analyzeMultiple']);

// Get top Indonesian stocks with analysis
Route::get('/top-stocks', [StockAnalysisController::class, 'topStocks']);

// Get stock data only (no analysis)
Route::get('/stock/{symbol}', [StockAnalysisController::class, 'getStock']);

// Compare stocks
Route::post('/compare', [StockAnalysisController::class, 'compareStocks']);

// Clear cache
Route::delete('/cache/{symbol}', [StockAnalysisController::class, 'clearCache']);

// Comprehensive Dashboard
Route::get('/dashboard/{symbol}', [StockAnalysisController::class, 'getDashboard']);

// Favorites Management
Route::get('/favorites', [StockAnalysisController::class, 'getFavorites']);
Route::post('/favorites', [StockAnalysisController::class, 'addFavorite']);
Route::delete('/favorites/{symbol}', [StockAnalysisController::class, 'removeFavorite']);
Route::get('/favorites/dashboard', [StockAnalysisController::class, 'favoritesDashboard']);

// Scan for buy opportunities
Route::get('/scan-opportunities', [StockAnalysisController::class, 'scanOpportunities']);
