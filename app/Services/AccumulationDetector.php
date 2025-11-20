<?php

namespace App\Services;

class AccumulationDetector
{
    /**
     * Detect accumulation or distribution patterns
     *
     * @param array $stockData
     * @return array
     */
    public function analyze(array $stockData): array
    {
        $closes = $stockData['historical_closes'] ?? [];
        $volumes = $stockData['historical_volumes'] ?? [];
        $currentPrice = $stockData['current_price'];
        $currentVolume = $stockData['volume'];
        $avgVolume = $stockData['avg_volume'];

        if (count($closes) < 20 || count($volumes) < 20) {
            return $this->defaultAccumulationAnalysis();
        }

        // Calculate On-Balance Volume (OBV)
        $obv = $this->calculateOBV($closes, $volumes);

        // Analyze volume trends
        $volumeTrend = $this->analyzeVolumeTrend($volumes, $closes);

        // Detect accumulation/distribution
        $phase = $this->detectPhase($closes, $volumes, $obv);

        // Calculate accumulation strength
        $strength = $this->calculateAccumulationStrength($closes, $volumes, $obv);

        // Volume price analysis
        $vpa = $this->volumePriceAnalysis($closes, $volumes);

        // Money flow analysis
        $moneyFlow = $this->analyzeMoneyFlow($stockData);

        // NEW: Accumulation duration
        $duration = $this->detectAccumulationDuration($closes, $volumes, $obv);

        // NEW: Accumulation magnitude
        $magnitude = $this->calculateAccumulationMagnitude($volumes, $avgVolume, $stockData['market_cap']);

        // NEW: Participant type detection
        $participants = $this->detectParticipantType($volumes, $closes, $currentVolume, $avgVolume);

        return [
            'phase' => $phase,
            'strength' => $strength,
            'obv_analysis' => $obv,
            'volume_trend' => $volumeTrend,
            'volume_price_analysis' => $vpa,
            'money_flow' => $moneyFlow,
            'duration' => $duration,
            'magnitude' => $magnitude,
            'participants' => $participants,
            'current_volume_vs_average' => [
                'current' => $currentVolume,
                'average' => $avgVolume,
                'ratio' => $avgVolume > 0 ? round($currentVolume / $avgVolume, 2) : 0,
                'status' => $this->getVolumeStatus($currentVolume, $avgVolume),
            ],
            'recommendation' => $this->getAccumulationRecommendation($phase, $strength, $volumeTrend),
        ];
    }

    /**
     * Calculate On-Balance Volume (OBV)
     */
    private function calculateOBV(array $closes, array $volumes): array
    {
        $obv = [0];

        for ($i = 1; $i < count($closes); $i++) {
            if ($closes[$i] > $closes[$i - 1]) {
                $obv[] = $obv[$i - 1] + $volumes[$i];
            } elseif ($closes[$i] < $closes[$i - 1]) {
                $obv[] = $obv[$i - 1] - $volumes[$i];
            } else {
                $obv[] = $obv[$i - 1];
            }
        }

        // Analyze OBV trend
        $recentOBV = array_slice($obv, -10);
        $olderOBV = array_slice($obv, -20, 10);

        $recentAvg = count($recentOBV) > 0 ? array_sum($recentOBV) / count($recentOBV) : 0;
        $olderAvg = count($olderOBV) > 0 ? array_sum($olderOBV) / count($olderOBV) : 0;

        $trend = 'Neutral';
        $trendStrength = 'Weak';

        if ($recentAvg > $olderAvg * 1.1) {
            $trend = 'Rising';
            $trendStrength = $recentAvg > $olderAvg * 1.2 ? 'Strong' : 'Moderate';
        } elseif ($recentAvg < $olderAvg * 0.9) {
            $trend = 'Falling';
            $trendStrength = $recentAvg < $olderAvg * 0.8 ? 'Strong' : 'Moderate';
        }

        return [
            'current_obv' => end($obv),
            'trend' => $trend,
            'trend_strength' => $trendStrength,
            'interpretation' => $this->interpretOBVTrend($trend, $trendStrength),
        ];
    }

    /**
     * Interpret OBV trend
     */
    private function interpretOBVTrend(string $trend, string $strength): string
    {
        if ($trend === 'Rising') {
            return "{$strength} accumulation - Money flowing into the stock";
        } elseif ($trend === 'Falling') {
            return "{$strength} distribution - Money flowing out of the stock";
        }
        return "Neutral - No clear accumulation or distribution";
    }

    /**
     * Analyze volume trend
     */
    private function analyzeVolumeTrend(array $volumes, array $closes): array
    {
        $recentVolumes = array_slice($volumes, -10);
        $olderVolumes = array_slice($volumes, -20, 10);
        $recentCloses = array_slice($closes, -10);
        $olderCloses = array_slice($closes, -20, 10);

        $recentAvgVol = count($recentVolumes) > 0 ? array_sum($recentVolumes) / count($recentVolumes) : 0;
        $olderAvgVol = count($olderVolumes) > 0 ? array_sum($olderVolumes) / count($olderVolumes) : 0;

        $recentAvgPrice = count($recentCloses) > 0 ? array_sum($recentCloses) / count($recentCloses) : 0;
        $olderAvgPrice = count($olderCloses) > 0 ? array_sum($olderCloses) / count($olderCloses) : 0;

        // Prevent division by zero
        $volumeChange = $olderAvgVol > 0 ? (($recentAvgVol - $olderAvgVol) / $olderAvgVol) * 100 : 0;
        $priceChange = $olderAvgPrice > 0 ? (($recentAvgPrice - $olderAvgPrice) / $olderAvgPrice) * 100 : 0;

        $pattern = '';
        $interpretation = '';

        if ($volumeChange > 20 && $priceChange > 0) {
            $pattern = 'Rising Volume + Rising Price';
            $interpretation = 'Strong accumulation - Bullish signal';
        } elseif ($volumeChange > 20 && $priceChange < 0) {
            $pattern = 'Rising Volume + Falling Price';
            $interpretation = 'Distribution or panic selling - Bearish signal';
        } elseif ($volumeChange < -20 && $priceChange > 0) {
            $pattern = 'Falling Volume + Rising Price';
            $interpretation = 'Weak rally - May reverse soon';
        } elseif ($volumeChange < -20 && $priceChange < 0) {
            $pattern = 'Falling Volume + Falling Price';
            $interpretation = 'Selling exhaustion - Potential bottom';
        } else {
            $pattern = 'Balanced Volume';
            $interpretation = 'No clear trend - Wait for confirmation';
        }

        return [
            'recent_avg_volume' => round($recentAvgVol, 0),
            'older_avg_volume' => round($olderAvgVol, 0),
            'volume_change_percent' => round($volumeChange, 2),
            'price_change_percent' => round($priceChange, 2),
            'pattern' => $pattern,
            'interpretation' => $interpretation,
        ];
    }

    /**
     * Detect current phase (Accumulation/Distribution/Markup/Markdown)
     */
    private function detectPhase(array $closes, array $volumes, array $obv): array
    {
        $recentCloses = array_slice($closes, -20);
        $recentVolumes = array_slice($volumes, -20);

        $priceVolatility = $this->calculateStdDev($recentCloses);
        $avgPrice = count($recentCloses) > 0 ? array_sum($recentCloses) / count($recentCloses) : 0;
        $avgVolume = count($recentVolumes) > 0 ? array_sum($recentVolumes) / count($recentVolumes) : 1;

        $currentPrice = end($closes);
        $currentVolume = end($volumes);

        $phase = '';
        $description = '';
        $color = '';

        // Wyckoff phases
        if ($obv['trend'] === 'Rising' && $currentPrice < $avgPrice * 1.05) {
            $phase = 'ACCUMULATION';
            $description = 'Smart money is accumulating at lower prices. Good time to build position.';
            $color = 'success';
        } elseif ($obv['trend'] === 'Rising' && $currentPrice > $avgPrice * 1.05) {
            $phase = 'MARKUP';
            $description = 'Price is rising with volume support. Uptrend in progress.';
            $color = 'success';
        } elseif ($obv['trend'] === 'Falling' && $currentPrice > $avgPrice * 0.95) {
            $phase = 'DISTRIBUTION';
            $description = 'Smart money is distributing at higher prices. Consider taking profits.';
            $color = 'danger';
        } elseif ($obv['trend'] === 'Falling' && $currentPrice < $avgPrice * 0.95) {
            $phase = 'MARKDOWN';
            $description = 'Price is falling with volume. Downtrend in progress. Avoid or short.';
            $color = 'danger';
        } else {
            $phase = 'NEUTRAL/CONSOLIDATION';
            $description = 'Stock is consolidating. Wait for clear direction.';
            $color = 'warning';
        }

        return [
            'current_phase' => $phase,
            'description' => $description,
            'color' => $color,
            'confidence' => $obv['trend_strength'],
        ];
    }

    /**
     * Calculate accumulation strength score
     */
    private function calculateAccumulationStrength(array $closes, array $volumes, array $obv): array
    {
        $score = 0;
        $maxScore = 100;
        $indicators = [];

        // OBV trend (30 points)
        if ($obv['trend'] === 'Rising') {
            if ($obv['trend_strength'] === 'Strong') {
                $score += 30;
                $indicators[] = 'Strong OBV uptrend (+30)';
            } else {
                $score += 20;
                $indicators[] = 'Moderate OBV uptrend (+20)';
            }
        } elseif ($obv['trend'] === 'Falling') {
            $indicators[] = 'OBV downtrend (0)';
        } else {
            $score += 10;
            $indicators[] = 'Neutral OBV (+10)';
        }

        // Volume analysis (30 points)
        $recentVolumes = array_slice($volumes, -10);
        $olderVolumes = array_slice($volumes, -20, 10);
        $recentAvg = count($recentVolumes) > 0 ? array_sum($recentVolumes) / count($recentVolumes) : 0;
        $olderAvg = count($olderVolumes) > 0 ? array_sum($olderVolumes) / count($olderVolumes) : 1;

        if ($recentAvg > $olderAvg * 1.2) {
            $score += 30;
            $indicators[] = 'Volume significantly increasing (+30)';
        } elseif ($recentAvg > $olderAvg) {
            $score += 20;
            $indicators[] = 'Volume moderately increasing (+20)';
        } else {
            $score += 5;
            $indicators[] = 'Volume not increasing (+5)';
        }

        // Price stability during volume increase (20 points)
        $recentCloses = array_slice($closes, -10);
        $volatility = $this->calculateStdDev($recentCloses);
        $avgPrice = count($recentCloses) > 0 ? array_sum($recentCloses) / count($recentCloses) : 0;
        $volatilityPercent = $avgPrice > 0 ? ($volatility / $avgPrice) * 100 : 0;

        if ($volatilityPercent < 2 && $recentAvg > $olderAvg) {
            $score += 20;
            $indicators[] = 'Price stable with volume increase - strong accumulation (+20)';
        } elseif ($volatilityPercent < 4) {
            $score += 10;
            $indicators[] = 'Moderate price stability (+10)';
        } else {
            $indicators[] = 'High volatility (0)';
        }

        // Price near lows (20 points)
        $currentPrice = end($closes);
        $min = min($recentCloses);
        $max = max($recentCloses);
        $range = $max - $min;

        if ($range > 0) {
            $positionInRange = (($currentPrice - $min) / $range) * 100;

            if ($positionInRange < 30) {
                $score += 20;
                $indicators[] = 'Price near recent lows - good accumulation zone (+20)';
            } elseif ($positionInRange < 50) {
                $score += 10;
                $indicators[] = 'Price in lower half of range (+10)';
            }
        }

        $strength = '';
        if ($score >= 70) {
            $strength = 'Strong Accumulation';
        } elseif ($score >= 50) {
            $strength = 'Moderate Accumulation';
        } elseif ($score >= 30) {
            $strength = 'Weak Accumulation';
        } else {
            $strength = 'No Clear Accumulation';
        }

        return [
            'score' => $score,
            'max_score' => $maxScore,
            'strength' => $strength,
            'indicators' => $indicators,
        ];
    }

    /**
     * Volume Price Analysis
     */
    private function volumePriceAnalysis(array $closes, array $volumes): array
    {
        $signals = [];

        // Check last 5 days
        $recentCloses = array_slice($closes, -5);
        $recentVolumes = array_slice($volumes, -5);
        $volumeSlice = array_slice($volumes, -20, 15);
        $avgVolume = count($volumeSlice) > 0 ? array_sum($volumeSlice) / count($volumeSlice) : 0;

        for ($i = 1; $i < count($recentCloses); $i++) {
            $priceChange = $recentCloses[$i - 1] > 0 ? (($recentCloses[$i] - $recentCloses[$i - 1]) / $recentCloses[$i - 1]) * 100 : 0;
            $volumeRatio = $avgVolume > 0 ? $recentVolumes[$i] / $avgVolume : 0;

            if ($priceChange > 2 && $volumeRatio > 1.5) {
                $signals[] = [
                    'type' => 'Bullish',
                    'signal' => 'Strong buying pressure',
                    'description' => 'Price up ' . round($priceChange, 2) . '% with ' . round($volumeRatio, 1) . 'x volume',
                ];
            } elseif ($priceChange < -2 && $volumeRatio > 1.5) {
                $signals[] = [
                    'type' => 'Bearish',
                    'signal' => 'Strong selling pressure',
                    'description' => 'Price down ' . round(abs($priceChange), 2) . '% with ' . round($volumeRatio, 1) . 'x volume',
                ];
            } elseif (abs($priceChange) < 1 && $volumeRatio > 2) {
                $signals[] = [
                    'type' => 'Neutral',
                    'signal' => 'High volume with low price change',
                    'description' => 'Possible accumulation or distribution in progress',
                ];
            }
        }

        if (empty($signals)) {
            $signals[] = [
                'type' => 'Neutral',
                'signal' => 'No significant volume-price patterns',
                'description' => 'Normal trading activity',
            ];
        }

        return $signals;
    }

    /**
     * Analyze money flow
     */
    private function analyzeMoneyFlow(array $stockData): array
    {
        $currentVolume = $stockData['volume'];
        $avgVolume = $stockData['avg_volume'];
        $changePercent = $stockData['change_percent'];

        $moneyFlowMultiplier = $changePercent / 100;
        $moneyFlowVolume = $currentVolume * $moneyFlowMultiplier;

        $status = '';
        $interpretation = '';

        if ($moneyFlowVolume > $avgVolume * 0.5) {
            $status = 'Strong Money Inflow';
            $interpretation = 'Significant buying interest with volume support';
        } elseif ($moneyFlowVolume > 0) {
            $status = 'Moderate Money Inflow';
            $interpretation = 'Positive money flow but moderate volume';
        } elseif ($moneyFlowVolume < -($avgVolume * 0.5)) {
            $status = 'Strong Money Outflow';
            $interpretation = 'Significant selling pressure with volume';
        } elseif ($moneyFlowVolume < 0) {
            $status = 'Moderate Money Outflow';
            $interpretation = 'Negative money flow but moderate volume';
        } else {
            $status = 'Neutral Money Flow';
            $interpretation = 'Balanced buying and selling';
        }

        return [
            'status' => $status,
            'interpretation' => $interpretation,
            'money_flow_volume' => round($moneyFlowVolume, 0),
        ];
    }

    /**
     * Get volume status
     */
    private function getVolumeStatus(float $current, float $average): string
    {
        if ($average == 0) return 'Unknown';

        $ratio = $current / $average;

        if ($ratio > 2) return 'Extremely High - Unusual activity';
        if ($ratio > 1.5) return 'Very High - Strong interest';
        if ($ratio > 1.2) return 'Above Average - Increased activity';
        if ($ratio > 0.8) return 'Normal - Regular activity';
        if ($ratio > 0.5) return 'Below Average - Low interest';
        return 'Very Low - Weak activity';
    }

    /**
     * Get recommendation based on accumulation analysis
     */
    private function getAccumulationRecommendation(array $phase, array $strength, array $volumeTrend): array
    {
        $action = '';
        $reasoning = [];

        if ($phase['current_phase'] === 'ACCUMULATION' && $strength['score'] >= 60) {
            $action = 'STRONG BUY - Accumulate Position';
            $reasoning[] = "Stock in accumulation phase with strong indicators";
            $reasoning[] = $phase['description'];
            $reasoning[] = "Accumulation strength: {$strength['strength']}";
        } elseif ($phase['current_phase'] === 'ACCUMULATION') {
            $action = 'BUY - Consider Building Position';
            $reasoning[] = "Stock showing accumulation signs";
            $reasoning[] = $phase['description'];
        } elseif ($phase['current_phase'] === 'MARKUP') {
            $action = 'HOLD/BUY - Uptrend Active';
            $reasoning[] = "Stock in markup phase - trend is your friend";
            $reasoning[] = $phase['description'];
        } elseif ($phase['current_phase'] === 'DISTRIBUTION') {
            $action = 'SELL/REDUCE - Take Profits';
            $reasoning[] = "Stock in distribution phase - smart money exiting";
            $reasoning[] = $phase['description'];
        } elseif ($phase['current_phase'] === 'MARKDOWN') {
            $action = 'AVOID/SELL - Downtrend Active';
            $reasoning[] = "Stock in markdown phase - downtrend in progress";
            $reasoning[] = $phase['description'];
        } else {
            $action = 'WAIT - No Clear Signal';
            $reasoning[] = "Stock in consolidation - wait for clear direction";
        }

        return [
            'action' => $action,
            'reasoning' => $reasoning,
            'confidence' => $phase['confidence'],
        ];
    }

    /**
     * Calculate standard deviation
     */
    private function calculateStdDev(array $values): float
    {
        $mean = array_sum($values) / count($values);
        $squareDiffs = array_map(function ($value) use ($mean) {
            return pow($value - $mean, 2);
        }, $values);

        return sqrt(array_sum($squareDiffs) / count($squareDiffs));
    }

    /**
     * Detect how long accumulation has been happening
     * Helps answer: "How long should I wait?" or "How long has it been accumulating?"
     */
    private function detectAccumulationDuration(array $closes, array $volumes, array $obv): array
    {
        // Only analyze if we have enough data
        if (count($closes) < 20) {
            return [
                'days' => 0,
                'weeks' => 0,
                'status' => 'Insufficient data',
                'interpretation' => 'Need more historical data',
                'wait_recommendation' => 'Wait for at least 20 days of data',
            ];
        }

        $accumulationDays = 0;
        $consecutiveDays = 0;
        $isCurrentlyAccumulating = false;

        // Walk backwards to find continuous accumulation period
        for ($i = count($closes) - 1; $i > 0; $i--) {
            $priceChange = $closes[$i] - $closes[$i - 1];
            $volumeSlice = array_slice($volumes, max(0, $i - 10), 10);
            $avgVolume = count($volumeSlice) > 0 ? array_sum($volumeSlice) / count($volumeSlice) : 1;
            $volumeRatio = $avgVolume > 0 ? $volumes[$i] / $avgVolume : 0;

            // Accumulation signs: high volume with stable/slight down price
            $isAccumulating = (
                $volumeRatio > 1.1 && // Volume above average
                abs($priceChange) < ($closes[$i] * 0.02) // Price relatively stable (<2% change)
            ) || (
                $obv['trend'] === 'Rising' && $priceChange >= 0 // OBV rising with price stable/up
            );

            if ($isAccumulating) {
                $consecutiveDays++;
                if ($i === count($closes) - 1) {
                    $isCurrentlyAccumulating = true;
                }
            } else {
                // Stop counting if we hit non-accumulation period
                if ($consecutiveDays > 0) {
                    break;
                }
            }
        }

        $accumulationDays = $consecutiveDays;
        $weeks = round($accumulationDays / 5, 1); // Trading weeks (5 days)

        // Determine status and recommendation
        $status = '';
        $interpretation = '';
        $waitRecommendation = '';

        if ($accumulationDays === 0) {
            $status = 'No Active Accumulation';
            $interpretation = 'Stock is not currently in accumulation phase';
            $waitRecommendation = 'Wait for accumulation signals to appear before entering';
        } elseif ($accumulationDays < 5) {
            $status = 'Early Stage Accumulation';
            $interpretation = 'Accumulation just started - very early phase';
            $waitRecommendation = $isCurrentlyAccumulating
                ? 'Consider waiting 1-2 more weeks to confirm accumulation pattern'
                : 'Accumulation may have ended prematurely';
        } elseif ($accumulationDays < 15) {
            $status = 'Active Accumulation';
            $interpretation = 'Accumulation is ongoing - good time to build position';
            $waitRecommendation = $isCurrentlyAccumulating
                ? 'Good entry zone - can start building position gradually'
                : 'Accumulation phase may be ending - monitor closely';
        } elseif ($accumulationDays < 30) {
            $status = 'Mature Accumulation';
            $interpretation = 'Extended accumulation period - smart money loading up';
            $waitRecommendation = $isCurrentlyAccumulating
                ? 'Strong accumulation - excellent entry opportunity, but may transition to markup soon'
                : 'Long accumulation complete - watch for markup phase breakout';
        } else {
            $status = 'Very Long Accumulation';
            $interpretation = 'Prolonged accumulation - big move may be coming';
            $waitRecommendation = $isCurrentlyAccumulating
                ? 'Extended accumulation often precedes strong markup - priority entry zone'
                : 'Very long accumulation ended - explosive move may be imminent';
        }

        return [
            'days' => $accumulationDays,
            'weeks' => $weeks,
            'status' => $status,
            'interpretation' => $interpretation,
            'wait_recommendation' => $waitRecommendation,
            'is_currently_accumulating' => $isCurrentlyAccumulating,
            'suggested_wait_time' => $this->getSuggestedWaitTime($accumulationDays, $isCurrentlyAccumulating),
        ];
    }

    /**
     * Get suggested wait time based on accumulation stage
     */
    private function getSuggestedWaitTime(int $accumulationDays, bool $isCurrentlyAccumulating): string
    {
        if (!$isCurrentlyAccumulating) {
            return 'N/A - Accumulation not active';
        }

        if ($accumulationDays < 5) {
            return '1-2 weeks (wait for confirmation)';
        } elseif ($accumulationDays < 15) {
            return '0-1 week (can enter now or wait for better price)';
        } elseif ($accumulationDays < 30) {
            return '0 days (enter now - may transition to markup soon)';
        } else {
            return '0 days (enter immediately - breakout may be imminent)';
        }
    }

    /**
     * Calculate accumulation magnitude (size/strength)
     * Helps answer: "How big is the accumulation?"
     */
    private function calculateAccumulationMagnitude(array $volumes, float $avgVolume, ?float $marketCap): array
    {
        if (count($volumes) < 20) {
            return [
                'total_volume' => 0,
                'vs_average' => 0,
                'value_estimate' => 0,
                'size' => 'Unknown',
                'interpretation' => 'Insufficient data',
            ];
        }

        // Calculate recent accumulation volume (last 20 days)
        $recentVolumes = array_slice($volumes, -20);
        $totalAccumulationVolume = array_sum($recentVolumes);
        $expectedVolume = $avgVolume * 20;
        $excessVolume = $totalAccumulationVolume - $expectedVolume;
        $magnitudeRatio = $avgVolume > 0 ? $totalAccumulationVolume / $expectedVolume : 0;

        // Estimate value of accumulation (if we have market cap)
        $valueEstimate = 0;
        if ($marketCap && $marketCap > 0) {
            // Rough estimate: excess volume as % of market cap
            $shareEstimate = $excessVolume;
            $priceEstimate = 1000; // Placeholder - we don't have shares outstanding
            $valueEstimate = $shareEstimate * $priceEstimate;
        }

        // Categorize magnitude
        $size = '';
        $interpretation = '';

        if ($magnitudeRatio >= 1.5) {
            $size = 'Very Large';
            $interpretation = 'Massive accumulation - volume ' . round(($magnitudeRatio - 1) * 100, 0) . '% above normal. Institutional activity likely.';
        } elseif ($magnitudeRatio >= 1.25) {
            $size = 'Large';
            $interpretation = 'Significant accumulation - volume ' . round(($magnitudeRatio - 1) * 100, 0) . '% above normal. Strong buying interest.';
        } elseif ($magnitudeRatio >= 1.1) {
            $size = 'Moderate';
            $interpretation = 'Moderate accumulation - volume ' . round(($magnitudeRatio - 1) * 100, 0) . '% above normal. Steady accumulation.';
        } elseif ($magnitudeRatio >= 0.9) {
            $size = 'Small';
            $interpretation = 'Minimal accumulation - volume near normal levels. Limited buying pressure.';
        } else {
            $size = 'Very Small / None';
            $interpretation = 'No significant accumulation - volume below normal. Low buying interest.';
        }

        return [
            'total_volume' => round($totalAccumulationVolume, 0),
            'expected_volume' => round($expectedVolume, 0),
            'excess_volume' => round($excessVolume, 0),
            'magnitude_ratio' => round($magnitudeRatio, 2),
            'vs_average_percent' => round(($magnitudeRatio - 1) * 100, 1),
            'size' => $size,
            'interpretation' => $interpretation,
            'value_estimate_idr' => round($valueEstimate, 0),
        ];
    }

    /**
     * Detect participant type (Retail vs Institution)
     * Helps answer: "Who is accumulating - retail or institution?"
     */
    private function detectParticipantType(array $volumes, array $closes, float $currentVolume, float $avgVolume): array
    {
        if (count($volumes) < 20 || count($closes) < 20) {
            return [
                'primary_type' => 'Unknown',
                'confidence' => 'Low',
                'indicators' => [],
                'interpretation' => 'Insufficient data for participant analysis',
            ];
        }

        $indicators = [];
        $institutionalScore = 0;
        $retailScore = 0;

        // 1. Volume Pattern Analysis
        $recentVolumes = array_slice($volumes, -20);
        $volumeStdDev = $this->calculateStdDev($recentVolumes);
        $volumeMean = count($recentVolumes) > 0 ? array_sum($recentVolumes) / count($recentVolumes) : 1;
        $coefficientOfVariation = $volumeMean > 0 ? ($volumeStdDev / $volumeMean) : 0;

        if ($coefficientOfVariation < 0.3) {
            // Consistent volume = Institutional (steady accumulation)
            $institutionalScore += 25;
            $indicators[] = 'Consistent volume pattern (Institutional sign)';
        } else {
            // Erratic volume = Retail (emotional trading)
            $retailScore += 25;
            $indicators[] = 'Erratic volume pattern (Retail sign)';
        }

        // 2. Volume Size Analysis
        $avgVolumeRatio = $avgVolume > 0 ? $volumeMean / $avgVolume : 0;
        if ($avgVolumeRatio > 1.5) {
            // Significantly high volume = Institutional
            $institutionalScore += 20;
            $indicators[] = 'Very high volume (' . round($avgVolumeRatio, 1) . 'x avg) - Institutional activity';
        } elseif ($avgVolumeRatio > 1.2) {
            $institutionalScore += 10;
            $indicators[] = 'Above average volume - Possible institutional interest';
        } else {
            $retailScore += 15;
            $indicators[] = 'Normal/low volume - Retail-dominated';
        }

        // 3. Price Behavior During Volume Spikes
        $priceVolatilityDuringHighVolume = 0;
        $highVolumeDays = 0;
        for ($i = 1; $i < count($recentVolumes); $i++) {
            if ($recentVolumes[$i] > $avgVolume * 1.3) {
                $highVolumeDays++;
                $priceChange = $closes[$i - 1] > 0 ? abs($closes[$i] - $closes[$i - 1]) / $closes[$i - 1] : 0;
                $priceVolatilityDuringHighVolume += $priceChange;
            }
        }

        if ($highVolumeDays > 0) {
            $avgVolatility = $priceVolatilityDuringHighVolume / $highVolumeDays;
            if ($avgVolatility < 0.015) {
                // High volume + low price volatility = Institutional (absorbing supply without moving price)
                $institutionalScore += 30;
                $indicators[] = 'High volume with low price movement - Classic institutional accumulation';
            } else {
                // High volume + high volatility = Retail (panic/euphoria)
                $retailScore += 20;
                $indicators[] = 'High volume with price volatility - Retail emotional trading';
            }
        }

        // 4. Trading Time Pattern (if we had intraday data, but we use daily)
        // For daily data, we check volume distribution consistency
        $firstHalf = array_slice($recentVolumes, 0, 10);
        $secondHalf = array_slice($recentVolumes, 10, 10);
        $firstHalfAvg = count($firstHalf) > 0 ? array_sum($firstHalf) / count($firstHalf) : 0;
        $secondHalfAvg = count($secondHalf) > 0 ? array_sum($secondHalf) / count($secondHalf) : 0;
        $maxAvg = max($firstHalfAvg, $secondHalfAvg);
        $distribution = $maxAvg > 0 ? abs($firstHalfAvg - $secondHalfAvg) / $maxAvg : 0;

        if ($distribution < 0.2) {
            // Even distribution = Institutional (systematic accumulation)
            $institutionalScore += 15;
            $indicators[] = 'Consistent accumulation over time - Institutional strategy';
        } else {
            // Uneven distribution = Retail (opportunistic)
            $retailScore += 10;
            $indicators[] = 'Uneven accumulation pattern - Retail behavior';
        }

        // 5. Price Trend vs Volume
        $recentCloses = array_slice($closes, -20);
        $priceChange = count($recentCloses) > 0 && $recentCloses[0] > 0
            ? ($recentCloses[count($recentCloses) - 1] - $recentCloses[0]) / $recentCloses[0]
            : 0;

        if ($priceChange > -0.05 && $priceChange < 0.05 && $avgVolumeRatio > 1.2) {
            // Flat price + high volume = Institutional accumulation (absorbing without markup)
            $institutionalScore += 10;
            $indicators[] = 'Price stable despite high volume - Stealth institutional accumulation';
        }

        // Determine primary participant type
        $totalScore = $institutionalScore + $retailScore;
        $institutionalPercent = $totalScore > 0 ? ($institutionalScore / $totalScore) * 100 : 50;

        $primaryType = '';
        $confidence = '';

        if ($institutionalPercent >= 70) {
            $primaryType = 'Institutional Dominant';
            $confidence = 'High';
        } elseif ($institutionalPercent >= 55) {
            $primaryType = 'Institutional Leaning';
            $confidence = 'Medium';
        } elseif ($institutionalPercent >= 45) {
            $primaryType = 'Mixed (Retail + Institutional)';
            $confidence = 'Medium';
        } elseif ($institutionalPercent >= 30) {
            $primaryType = 'Retail Leaning';
            $confidence = 'Medium';
        } else {
            $primaryType = 'Retail Dominant';
            $confidence = 'High';
        }

        $interpretation = $this->interpretParticipantType($primaryType, $institutionalPercent);

        return [
            'primary_type' => $primaryType,
            'institutional_score' => $institutionalScore,
            'retail_score' => $retailScore,
            'institutional_percent' => round($institutionalPercent, 1),
            'retail_percent' => round(100 - $institutionalPercent, 1),
            'confidence' => $confidence,
            'indicators' => $indicators,
            'interpretation' => $interpretation,
        ];
    }

    /**
     * Interpret participant type
     */
    private function interpretParticipantType(string $type, float $institutionalPercent): string
    {
        if (str_contains($type, 'Institutional Dominant')) {
            return 'Strong institutional accumulation detected. Smart money is loading up. This is typically a very bullish sign as institutions have better research and longer time horizons. Follow the smart money!';
        } elseif (str_contains($type, 'Institutional Leaning')) {
            return 'Institutional buyers are likely active. Combined with some retail participation. Generally bullish as institutional involvement suggests confidence in the stock.';
        } elseif (str_contains($type, 'Mixed')) {
            return 'Both retail and institutional investors are participating. Healthy mix of participants. Watch for which side gains dominance.';
        } elseif (str_contains($type, 'Retail Leaning')) {
            return 'Mostly retail-driven activity. Institutions are less involved. Be cautious as retail can be more emotional and prone to reversals. Look for institutional confirmation.';
        } else {
            return 'Heavy retail participation with minimal institutional interest. Higher risk as retail tends to be late to trends. Wait for institutional involvement for confirmation.';
        }
    }

    /**
     * Default accumulation analysis
     */
    private function defaultAccumulationAnalysis(): array
    {
        return [
            'phase' => [
                'current_phase' => 'UNKNOWN',
                'description' => 'Insufficient data for accumulation analysis',
                'color' => 'secondary',
                'confidence' => 'None',
            ],
            'strength' => [
                'score' => 0,
                'strength' => 'Unknown',
                'indicators' => [],
            ],
            'obv_analysis' => [
                'trend' => 'Unknown',
                'interpretation' => 'Need more data',
            ],
            'volume_trend' => [
                'pattern' => 'Unknown',
                'interpretation' => 'Need more data',
            ],
            'volume_price_analysis' => [],
            'money_flow' => [
                'status' => 'Unknown',
                'interpretation' => 'Insufficient data',
            ],
            'duration' => [
                'days' => 0,
                'status' => 'Unknown',
                'interpretation' => 'Need more data',
            ],
            'magnitude' => [
                'size' => 'Unknown',
                'interpretation' => 'Need more data',
            ],
            'participants' => [
                'primary_type' => 'Unknown',
                'interpretation' => 'Need more data',
            ],
            'recommendation' => [
                'action' => 'WAIT - Insufficient Data',
                'reasoning' => ['Need more historical data for accurate accumulation analysis'],
            ],
        ];
    }
}
