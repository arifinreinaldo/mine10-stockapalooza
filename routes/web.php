<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StockAnalysisController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [StockAnalysisController::class, 'index']);
