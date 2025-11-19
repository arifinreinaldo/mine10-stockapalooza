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

        return [
            'phase' => $phase,
            'strength' => $strength,
            'obv_analysis' => $obv,
            'volume_trend' => $volumeTrend,
            'volume_price_analysis' => $vpa,
            'money_flow' => $moneyFlow,
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

        $recentAvg = array_sum($recentOBV) / count($recentOBV);
        $olderAvg = array_sum($olderOBV) / count($olderOBV);

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

        $recentAvgVol = array_sum($recentVolumes) / count($recentVolumes);
        $olderAvgVol = array_sum($olderVolumes) / count($olderVolumes);

        $recentAvgPrice = array_sum($recentCloses) / count($recentCloses);
        $olderAvgPrice = array_sum($olderCloses) / count($olderCloses);

        $volumeChange = (($recentAvgVol - $olderAvgVol) / $olderAvgVol) * 100;
        $priceChange = (($recentAvgPrice - $olderAvgPrice) / $olderAvgPrice) * 100;

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
        $avgPrice = array_sum($recentCloses) / count($recentCloses);
        $avgVolume = array_sum($recentVolumes) / count($recentVolumes);

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
        $recentAvg = array_sum($recentVolumes) / count($recentVolumes);
        $olderAvg = array_sum($olderVolumes) / count($olderVolumes);

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
        $avgPrice = array_sum($recentCloses) / count($recentCloses);
        $volatilityPercent = ($volatility / $avgPrice) * 100;

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
        $avgVolume = array_sum(array_slice($volumes, -20, 15)) / 15;

        for ($i = 1; $i < count($recentCloses); $i++) {
            $priceChange = (($recentCloses[$i] - $recentCloses[$i - 1]) / $recentCloses[$i - 1]) * 100;
            $volumeRatio = $recentVolumes[$i] / $avgVolume;

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
            'recommendation' => [
                'action' => 'WAIT - Insufficient Data',
                'reasoning' => ['Need more historical data for accurate accumulation analysis'],
            ],
        ];
    }
}
