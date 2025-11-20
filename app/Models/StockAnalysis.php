<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockAnalysis extends Model
{
    use HasFactory;

    protected $fillable = [
        'symbol',
        'market',
        'price',
        'rsi',
        'above_sma',
        'volume_ratio',
        'volatility_rating',
        'pe_ratio',
        'pb_ratio',
        'roe',
        'eps',
        'accumulation_phase',
        'accumulation_strength',
        'accumulation_days',
        'participant_type',
        'institutional_percent',
        'swing_score',
        'avg_swing_percent',
        'trend_pattern',
        'overall_score',
        'recommendation',
        'price_after_7days',
        'price_change_7days_percent',
        'went_up_5pct',
    ];

    protected $casts = [
        'above_sma' => 'boolean',
        'went_up_5pct' => 'boolean',
    ];
}
