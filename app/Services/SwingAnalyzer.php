<?php

namespace App\Services;

class SwingAnalyzer
{
    /**
     * Analyze swing patterns and volatility
     *
     * @param array $stockData
     * @return array
     */
    public function analyze(array $stockData): array
    {
        $closes = $stockData['historical_closes'] ?? [];
        $volumes = $stockData['historical_volumes'] ?? [];
        $currentPrice = $stockData['current_price'];

        if (count($closes) < 20) {
            return $this->defaultSwingAnalysis($currentPrice);
        }

        // Calculate swing size metrics
        $swingSize = $this->calculateSwingSize($closes);

        // Detect swing pattern
        $swingPattern = $this->detectSwingPattern($closes);

        // Calculate volatility
        $volatility = $this->calculateVolatility($closes);

        // Bollinger Bands
        $bollingerBands = $this->calculateBollingerBands($closes);

        // Swing trading signals
        $signals = $this->generateSwingSignals($currentPrice, $swingPattern, $bollingerBands);

        // Swing duration analysis
        $duration = $this->analyzeSwingDuration($closes);

        return [
            'swing_size' => $swingSize,
            'swing_pattern' => $swingPattern,
            'volatility' => $volatility,
            'bollinger_bands' => $bollingerBands,
            'swing_signals' => $signals,
            'swing_duration' => $duration,
            'swing_rating' => $this->rateSwingTrading($swingSize, $volatility, $swingPattern),
        ];
    }

    /**
     * Calculate swing size
     */
    private function calculateSwingSize(array $closes): array
    {
        $recent = array_slice($closes, -20); // Last 20 days

        // Find swings (local highs and lows)
        $swings = [];
        $swingSizes = [];

        for ($i = 1; $i < count($recent) - 1; $i++) {
            // Local high
            if ($recent[$i] > $recent[$i - 1] && $recent[$i] > $recent[$i + 1]) {
                $swings[] = ['type' => 'high', 'price' => $recent[$i], 'index' => $i];
            }
            // Local low
            if ($recent[$i] < $recent[$i - 1] && $recent[$i] < $recent[$i + 1]) {
                $swings[] = ['type' => 'low', 'price' => $recent[$i], 'index' => $i];
            }
        }

        // Calculate swing sizes
        for ($i = 0; $i < count($swings) - 1; $i++) {
            $size = abs($swings[$i + 1]['price'] - $swings[$i]['price']);
            $sizePercent = $swings[$i]['price'] > 0 ? ($size / $swings[$i]['price']) * 100 : 0;
            $swingSizes[] = $sizePercent;
        }

        $avgSwingSize = !empty($swingSizes) ? array_sum($swingSizes) / count($swingSizes) : 0;
        $maxSwingSize = !empty($swingSizes) ? max($swingSizes) : 0;
        $minSwingSize = !empty($swingSizes) ? min($swingSizes) : 0;

        $currentPrice = end($closes);
        $recentHigh = max($recent);
        $recentLow = min($recent);
        $currentSwingRange = $recentLow > 0 ? (($recentHigh - $recentLow) / $recentLow) * 100 : 0;

        return [
            'average_swing_percent' => round($avgSwingSize, 2),
            'max_swing_percent' => round($maxSwingSize, 2),
            'min_swing_percent' => round($minSwingSize, 2),
            'current_swing_range_percent' => round($currentSwingRange, 2),
            'swing_count' => count($swingSizes),
            'swing_size_category' => $this->categorizeSwingSize($avgSwingSize),
            'recent_high' => round($recentHigh, 2),
            'recent_low' => round($recentLow, 2),
        ];
    }

    /**
     * Categorize swing size
     */
    private function categorizeSwingSize(float $swingPercent): string
    {
        if ($swingPercent > 10) return 'Very Large (High volatility)';
        if ($swingPercent > 5) return 'Large (Good for swing trading)';
        if ($swingPercent > 3) return 'Moderate (Decent swings)';
        if ($swingPercent > 1) return 'Small (Limited swing potential)';
        return 'Very Small (Low volatility)';
    }

    /**
     * Detect current swing pattern
     */
    private function detectSwingPattern(array $closes): array
    {
        $recent = array_slice($closes, -20);

        // Find higher highs, higher lows (uptrend)
        // Find lower highs, lower lows (downtrend)

        $highs = [];
        $lows = [];

        for ($i = 1; $i < count($recent) - 1; $i++) {
            if ($recent[$i] > $recent[$i - 1] && $recent[$i] > $recent[$i + 1]) {
                $highs[] = $recent[$i];
            }
            if ($recent[$i] < $recent[$i - 1] && $recent[$i] < $recent[$i + 1]) {
                $lows[] = $recent[$i];
            }
        }

        $pattern = 'Sideways';
        $description = 'Stock is moving in a range without clear trend';

        if (count($highs) >= 2 && count($lows) >= 2) {
            $highsAscending = true;
            $lowsAscending = true;

            for ($i = 0; $i < count($highs) - 1; $i++) {
                if ($highs[$i + 1] <= $highs[$i]) {
                    $highsAscending = false;
                    break;
                }
            }

            for ($i = 0; $i < count($lows) - 1; $i++) {
                if ($lows[$i + 1] <= $lows[$i]) {
                    $lowsAscending = false;
                    break;
                }
            }

            if ($highsAscending && $lowsAscending) {
                $pattern = 'Higher Highs & Higher Lows';
                $description = 'Strong uptrend - each swing higher than previous. Good for swing long positions.';
            } elseif (!$highsAscending && !$lowsAscending) {
                // Check if lower highs and lower lows
                $highsDescending = true;
                $lowsDescending = true;

                for ($i = 0; $i < count($highs) - 1; $i++) {
                    if ($highs[$i + 1] >= $highs[$i]) {
                        $highsDescending = false;
                        break;
                    }
                }

                for ($i = 0; $i < count($lows) - 1; $i++) {
                    if ($lows[$i + 1] >= $lows[$i]) {
                        $lowsDescending = false;
                        break;
                    }
                }

                if ($highsDescending && $lowsDescending) {
                    $pattern = 'Lower Highs & Lower Lows';
                    $description = 'Strong downtrend - each swing lower than previous. Caution for long positions.';
                }
            }
        }

        return [
            'pattern' => $pattern,
            'description' => $description,
            'highs_count' => count($highs),
            'lows_count' => count($lows),
            'latest_high' => !empty($highs) ? round(max($highs), 2) : null,
            'latest_low' => !empty($lows) ? round(min($lows), 2) : null,
        ];
    }

    /**
     * Calculate volatility (standard deviation)
     */
    private function calculateVolatility(array $closes, int $period = 20): array
    {
        $recent = array_slice($closes, -$period);

        // Calculate daily returns
        $returns = [];
        for ($i = 1; $i < count($recent); $i++) {
            $returns[] = $recent[$i - 1] > 0 ? (($recent[$i] - $recent[$i - 1]) / $recent[$i - 1]) * 100 : 0;
        }

        $mean = count($returns) > 0 ? array_sum($returns) / count($returns) : 0;

        // Standard deviation
        $squareDiffs = array_map(function ($return) use ($mean) {
            return pow($return - $mean, 2);
        }, $returns);

        $variance = count($squareDiffs) > 0 ? array_sum($squareDiffs) / count($squareDiffs) : 0;
        $stdDev = sqrt($variance);

        // Annualized volatility (assuming 252 trading days)
        $annualizedVol = $stdDev * sqrt(252);

        return [
            'daily_volatility_percent' => round($stdDev, 2),
            'annualized_volatility_percent' => round($annualizedVol, 2),
            'volatility_rating' => $this->rateVolatility($stdDev),
            'avg_daily_return_percent' => round($mean, 2),
        ];
    }

    /**
     * Rate volatility
     */
    private function rateVolatility(float $stdDev): string
    {
        if ($stdDev > 5) return 'Extremely High';
        if ($stdDev > 3) return 'High';
        if ($stdDev > 2) return 'Moderate';
        if ($stdDev > 1) return 'Low';
        return 'Very Low';
    }

    /**
     * Calculate Bollinger Bands
     */
    private function calculateBollingerBands(array $closes, int $period = 20, float $stdDevMultiplier = 2): array
    {
        $recent = array_slice($closes, -$period);

        // Safety check for empty array
        if (count($recent) === 0) {
            return [
                'upper_band' => 0,
                'middle_band' => 0,
                'lower_band' => 0,
                'band_width_percent' => 0,
                'price_position_percent' => 50,
                'squeeze_status' => 'Unknown',
            ];
        }

        $sma = array_sum($recent) / count($recent);

        // Standard deviation
        $squareDiffs = array_map(function ($price) use ($sma) {
            return pow($price - $sma, 2);
        }, $recent);

        $variance = count($squareDiffs) > 0 ? array_sum($squareDiffs) / count($squareDiffs) : 0;
        $stdDev = sqrt($variance);

        $upperBand = $sma + ($stdDev * $stdDevMultiplier);
        $lowerBand = $sma - ($stdDev * $stdDevMultiplier);

        $currentPrice = end($closes);
        $bandRange = $upperBand - $lowerBand;

        // Prevent division by zero
        $bandWidth = $sma > 0 ? (($upperBand - $lowerBand) / $sma) * 100 : 0;
        $pricePosition = $bandRange > 0 ? (($currentPrice - $lowerBand) / $bandRange) * 100 : 50;

        return [
            'upper_band' => round($upperBand, 2),
            'middle_band' => round($sma, 2),
            'lower_band' => round($lowerBand, 2),
            'band_width_percent' => round($bandWidth, 2),
            'price_position_percent' => round($pricePosition, 2),
            'squeeze_status' => $this->getBandSqueezeStatus($bandWidth),
        ];
    }

    /**
     * Determine if bands are squeezing
     */
    private function getBandSqueezeStatus(float $bandWidth): string
    {
        if ($bandWidth < 5) return 'Tight Squeeze - Breakout likely';
        if ($bandWidth < 10) return 'Moderate - Watch for expansion';
        if ($bandWidth < 20) return 'Normal - Regular volatility';
        return 'Wide - High volatility period';
    }

    /**
     * Generate swing trading signals
     */
    private function generateSwingSignals(float $currentPrice, array $pattern, array $bb): array
    {
        $signals = [];

        // Bollinger Band signals
        if ($bb['price_position_percent'] < 20) {
            $signals[] = [
                'type' => 'BUY',
                'strength' => 'Strong',
                'reason' => 'Price near lower Bollinger Band - oversold condition',
                'indicator' => 'Bollinger Bands',
            ];
        } elseif ($bb['price_position_percent'] > 80) {
            $signals[] = [
                'type' => 'SELL',
                'strength' => 'Strong',
                'reason' => 'Price near upper Bollinger Band - overbought condition',
                'indicator' => 'Bollinger Bands',
            ];
        }

        // Pattern signals
        if ($pattern['pattern'] === 'Higher Highs & Higher Lows') {
            $signals[] = [
                'type' => 'BUY',
                'strength' => 'Medium',
                'reason' => 'Uptrend pattern detected - swing long opportunity',
                'indicator' => 'Swing Pattern',
            ];
        } elseif ($pattern['pattern'] === 'Lower Highs & Lower Lows') {
            $signals[] = [
                'type' => 'SELL',
                'strength' => 'Medium',
                'reason' => 'Downtrend pattern detected - avoid long positions',
                'indicator' => 'Swing Pattern',
            ];
        }

        // Band squeeze signals
        if (strpos($bb['squeeze_status'], 'Tight Squeeze') !== false) {
            $signals[] = [
                'type' => 'ALERT',
                'strength' => 'High',
                'reason' => 'Bollinger Bands squeezing - expect significant move soon',
                'indicator' => 'Band Squeeze',
            ];
        }

        if (empty($signals)) {
            $signals[] = [
                'type' => 'HOLD',
                'strength' => 'Neutral',
                'reason' => 'No clear swing signals at current price level',
                'indicator' => 'Overall',
            ];
        }

        return $signals;
    }

    /**
     * Analyze swing duration
     */
    private function analyzeSwingDuration(array $closes): array
    {
        $recent = array_slice($closes, -50);
        $swings = [];

        $inSwing = false;
        $swingStart = 0;
        $swingType = null;

        for ($i = 1; $i < count($recent); $i++) {
            if (!$inSwing) {
                if ($recent[$i] > $recent[$i - 1]) {
                    $inSwing = true;
                    $swingStart = $i;
                    $swingType = 'up';
                } elseif ($recent[$i] < $recent[$i - 1]) {
                    $inSwing = true;
                    $swingStart = $i;
                    $swingType = 'down';
                }
            } else {
                // Check for swing reversal
                if (($swingType === 'up' && $recent[$i] < $recent[$i - 1]) ||
                    ($swingType === 'down' && $recent[$i] > $recent[$i - 1])) {
                    $duration = $i - $swingStart;
                    $swings[] = ['type' => $swingType, 'duration' => $duration];
                    $inSwing = false;
                }
            }
        }

        if (empty($swings)) {
            return [
                'average_duration_days' => 0,
                'typical_swing_period' => 'Unknown',
            ];
        }

        $durations = array_column($swings, 'duration');
        $avgDuration = array_sum($durations) / count($durations);

        $period = 'Unknown';
        if ($avgDuration <= 3) {
            $period = 'Very Short-term (1-3 days) - Day trading';
        } elseif ($avgDuration <= 7) {
            $period = 'Short-term (3-7 days) - Quick swings';
        } elseif ($avgDuration <= 14) {
            $period = 'Medium-term (1-2 weeks) - Ideal for swing trading';
        } else {
            $period = 'Long-term (2+ weeks) - Position trading';
        }

        return [
            'average_duration_days' => round($avgDuration, 1),
            'typical_swing_period' => $period,
            'swing_count' => count($swings),
        ];
    }

    /**
     * Rate stock for swing trading
     */
    private function rateSwingTrading(array $swingSize, array $volatility, array $pattern): array
    {
        $score = 0;
        $maxScore = 100;
        $reasons = [];

        // Score based on swing size (40 points)
        if ($swingSize['average_swing_percent'] >= 5 && $swingSize['average_swing_percent'] <= 15) {
            $score += 40;
            $reasons[] = "Excellent swing size ({$swingSize['average_swing_percent']}%) - ideal for swing trading";
        } elseif ($swingSize['average_swing_percent'] >= 3) {
            $score += 25;
            $reasons[] = "Good swing size ({$swingSize['average_swing_percent']}%) - decent opportunities";
        } else {
            $score += 10;
            $reasons[] = "Limited swing size ({$swingSize['average_swing_percent']}%) - smaller profit potential";
        }

        // Score based on volatility (30 points)
        $volRating = $volatility['volatility_rating'];
        if ($volRating === 'Moderate' || $volRating === 'High') {
            $score += 30;
            $reasons[] = "{$volRating} volatility - good movement for swing trades";
        } elseif ($volRating === 'Low') {
            $score += 15;
            $reasons[] = "Low volatility - limited but safer swings";
        } else {
            $score += 5;
            $reasons[] = "{$volRating} volatility - may be challenging";
        }

        // Score based on pattern (30 points)
        if (strpos($pattern['pattern'], 'Higher') !== false) {
            $score += 30;
            $reasons[] = "Clear uptrend pattern - favorable for swing longs";
        } elseif (strpos($pattern['pattern'], 'Lower') !== false) {
            $score += 15;
            $reasons[] = "Downtrend pattern - only for experienced short sellers";
        } else {
            $score += 20;
            $reasons[] = "Sideways pattern - range-bound swing opportunities";
        }

        $rating = '';
        if ($score >= 80) {
            $rating = 'Excellent for Swing Trading';
        } elseif ($score >= 60) {
            $rating = 'Good for Swing Trading';
        } elseif ($score >= 40) {
            $rating = 'Moderate for Swing Trading';
        } else {
            $rating = 'Poor for Swing Trading';
        }

        return [
            'score' => $score,
            'max_score' => $maxScore,
            'rating' => $rating,
            'reasons' => $reasons,
        ];
    }

    /**
     * Default swing analysis
     */
    private function defaultSwingAnalysis(float $currentPrice): array
    {
        return [
            'swing_size' => [
                'average_swing_percent' => 0,
                'swing_size_category' => 'Unknown - Insufficient data',
            ],
            'swing_pattern' => [
                'pattern' => 'Unknown',
                'description' => 'Insufficient historical data',
            ],
            'volatility' => [
                'daily_volatility_percent' => 0,
                'volatility_rating' => 'Unknown',
            ],
            'bollinger_bands' => [],
            'swing_signals' => [],
            'swing_duration' => [
                'average_duration_days' => 0,
                'typical_swing_period' => 'Unknown',
            ],
            'swing_rating' => [
                'rating' => 'Insufficient Data',
                'reasons' => ['Need more historical data for swing analysis'],
            ],
        ];
    }
}
