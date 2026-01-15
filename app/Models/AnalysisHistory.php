<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnalysisHistory extends Model
{
    use HasFactory;

    protected $table = 'analysis_history';

    protected $fillable = [
        'symbol',
        'market',
        'stock_name',
        'price_at_analysis',
        'phase',
        'phase_confidence',
        'accumulation_strength',
        'overall_score',
        'recommendation',
        'institutional_percent',
        'rsi',
    ];

    protected $casts = [
        'price_at_analysis' => 'decimal:2',
        'overall_score' => 'decimal:2',
        'institutional_percent' => 'decimal:2',
        'rsi' => 'decimal:2',
    ];

    /**
     * Scope to filter by phase
     */
    public function scopeByPhase($query, string $phase)
    {
        return $query->where('phase', $phase);
    }

    /**
     * Scope to get recent records within specified days
     */
    public function scopeRecent($query, int $days = 90)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Trim old history records (older than 3 months)
     * Call this periodically or after inserts
     */
    public static function trimOldRecords(int $monthsToKeep = 3): int
    {
        $cutoffDate = now()->subMonths($monthsToKeep);

        return static::where('created_at', '<', $cutoffDate)->delete();
    }

    /**
     * Boot method to automatically trim old records periodically
     */
    protected static function booted()
    {
        // Trim old records after creating new ones (1% chance to avoid doing it every time)
        static::created(function ($model) {
            if (rand(1, 100) === 1) {
                static::trimOldRecords();
            }
        });
    }
}
