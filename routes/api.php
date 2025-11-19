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
