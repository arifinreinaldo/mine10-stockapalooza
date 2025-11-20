<?php

namespace App\Services;

class EntryExitAnalyzer
{
    /**
     * Calculate entry and exit price recommendations
     *
     * @param array $stockData
     * @return array
     */
    public function analyze(array $stockData): array
    {
        $currentPrice = $stockData['current_price'];
        $closes = $stockData['historical_closes'] ?? [];

        if (count($closes) < 20) {
            return $this->defaultRecommendation($currentPrice);
        }

        // Calculate support and resistance levels
        $supportResistance = $this->calculateSupportResistance($closes, $currentPrice);

        // Calculate Fibonacci levels
        $fibonacci = $this->calculateFibonacciLevels($closes);

        // Calculate ATR-based levels
        $atrLevels = $this->calculateATRLevels($closes, $currentPrice);

        // Determine entry zones
        $entryZones = $this->determineEntryZones($currentPrice, $supportResistance, $fibonacci);

        // Determine exit zones
        $exitZones = $this->determineExitZones($currentPrice, $supportResistance, $fibonacci);

        // Calculate risk-reward ratio
        $riskReward = $this->calculateRiskReward($entryZones, $exitZones, $currentPrice);

        return [
            'current_price' => $currentPrice,
            'entry_recommendation' => $entryZones,
            'exit_recommendation' => $exitZones,
            'support_levels' => $supportResistance['support'],
            'resistance_levels' => $supportResistance['resistance'],
            'fibonacci_levels' => $fibonacci,
            'atr_levels' => $atrLevels,
            'risk_reward_ratio' => $riskReward,
            'position_recommendation' => $this->getPositionRecommendation($currentPrice, $entryZones, $supportResistance),
        ];
    }

    /**
     * Calculate support and resistance levels
     */
    private function calculateSupportResistance(array $closes, float $currentPrice): array
    {
        $recentCloses = array_slice($closes, -50); // Last 50 days

        // Find local highs and lows
        $pivots = $this->findPivotPoints($recentCloses);

        // Cluster similar prices to find strong levels
        $supports = [];
        $resistances = [];

        foreach ($pivots['lows'] as $low) {
            if ($low < $currentPrice) {
                $supports[] = $low;
            }
        }

        foreach ($pivots['highs'] as $high) {
            if ($high > $currentPrice) {
                $resistances[] = $high;
            }
        }

        // Get unique levels by clustering
        $supports = $this->clusterLevels($supports, $currentPrice * 0.02); // 2% tolerance
        $resistances = $this->clusterLevels($resistances, $currentPrice * 0.02);

        // Sort and take top 3
        rsort($supports);
        sort($resistances);

        return [
            'support' => array_slice($supports, 0, 3),
            'resistance' => array_slice($resistances, 0, 3),
        ];
    }

    /**
     * Find pivot points (local highs and lows)
     */
    private function findPivotPoints(array $prices, int $window = 5): array
    {
        $highs = [];
        $lows = [];
        $count = count($prices);

        for ($i = $window; $i < $count - $window; $i++) {
            $isHigh = true;
            $isLow = true;

            // Check if it's a local high or low
            for ($j = 1; $j <= $window; $j++) {
                if ($prices[$i] <= $prices[$i - $j] || $prices[$i] <= $prices[$i + $j]) {
                    $isHigh = false;
                }
                if ($prices[$i] >= $prices[$i - $j] || $prices[$i] >= $prices[$i + $j]) {
                    $isLow = false;
                }
            }

            if ($isHigh) {
                $highs[] = $prices[$i];
            }
            if ($isLow) {
                $lows[] = $prices[$i];
            }
        }

        return ['highs' => $highs, 'lows' => $lows];
    }

    /**
     * Cluster similar price levels
     */
    private function clusterLevels(array $levels, float $tolerance): array
    {
        if (empty($levels)) {
            return [];
        }

        sort($levels);
        $clusters = [];
        $currentCluster = [$levels[0]];

        for ($i = 1; $i < count($levels); $i++) {
            if (abs($levels[$i] - $levels[$i - 1]) <= $tolerance) {
                $currentCluster[] = $levels[$i];
            } else {
                if (count($currentCluster) > 0) {
                    $clusters[] = array_sum($currentCluster) / count($currentCluster);
                }
                $currentCluster = [$levels[$i]];
            }
        }

        // Add last cluster
        if (count($currentCluster) > 0) {
            $clusters[] = array_sum($currentCluster) / count($currentCluster);
        }

        return $clusters;
    }

    /**
     * Calculate Fibonacci retracement levels
     */
    private function calculateFibonacciLevels(array $closes): array
    {
        $recent = array_slice($closes, -50);
        $high = max($recent);
        $low = min($recent);
        $diff = $high - $low;

        return [
            'level_0' => round($high, 2),
            'level_236' => round($high - ($diff * 0.236), 2),
            'level_382' => round($high - ($diff * 0.382), 2),
            'level_500' => round($high - ($diff * 0.500), 2),
            'level_618' => round($high - ($diff * 0.618), 2),
            'level_786' => round($high - ($diff * 0.786), 2),
            'level_100' => round($low, 2),
        ];
    }

    /**
     * Calculate ATR-based stop loss and take profit levels
     */
    private function calculateATRLevels(array $closes, float $currentPrice): array
    {
        $atr = $this->calculateATR($closes);

        return [
            'atr_value' => round($atr, 2),
            'stop_loss_1x' => round($currentPrice - $atr, 2),
            'stop_loss_2x' => round($currentPrice - ($atr * 2), 2),
            'take_profit_1x' => round($currentPrice + $atr, 2),
            'take_profit_2x' => round($currentPrice + ($atr * 2), 2),
            'take_profit_3x' => round($currentPrice + ($atr * 3), 2),
        ];
    }

    /**
     * Calculate Average True Range (ATR)
     */
    private function calculateATR(array $closes, int $period = 14): float
    {
        if (count($closes) < $period + 1) {
            return 0;
        }

        $trueRanges = [];

        for ($i = 1; $i < count($closes); $i++) {
            $high = max($closes[$i], $closes[$i - 1]);
            $low = min($closes[$i], $closes[$i - 1]);
            $trueRanges[] = $high - $low;
        }

        $recentTR = array_slice($trueRanges, -$period);
        return $period > 0 ? array_sum($recentTR) / $period : 0;
    }

    /**
     * Determine entry zones
     */
    private function determineEntryZones(float $currentPrice, array $sr, array $fib): array
    {
        $zones = [];

        // Conservative entry (near support)
        if (!empty($sr['support'])) {
            $zones['conservative'] = [
                'price' => $sr['support'][0],
                'range' => [$sr['support'][0] * 0.98, $sr['support'][0] * 1.02],
                'description' => 'Strong support level - safest entry',
                'distance_percent' => $currentPrice > 0 ? round((($sr['support'][0] - $currentPrice) / $currentPrice) * 100, 2) : 0,
            ];
        }

        // Moderate entry (Fibonacci 0.618)
        $zones['moderate'] = [
            'price' => $fib['level_618'],
            'range' => [$fib['level_618'] * 0.99, $fib['level_618'] * 1.01],
            'description' => 'Golden ratio retracement - balanced entry',
            'distance_percent' => round((($fib['level_618'] - $currentPrice) / $currentPrice) * 100, 2),
        ];

        // Aggressive entry (Fibonacci 0.382)
        $zones['aggressive'] = [
            'price' => $fib['level_382'],
            'range' => [$fib['level_382'] * 0.99, $fib['level_382'] * 1.01],
            'description' => 'Shallow retracement - aggressive entry',
            'distance_percent' => round((($fib['level_382'] - $currentPrice) / $currentPrice) * 100, 2),
        ];

        return $zones;
    }

    /**
     * Determine exit zones
     */
    private function determineExitZones(float $currentPrice, array $sr, array $fib): array
    {
        $zones = [];

        // First target (near resistance or Fib 0.382 above current)
        if (!empty($sr['resistance'])) {
            $zones['target_1'] = [
                'price' => $sr['resistance'][0],
                'description' => 'First resistance - take partial profit',
                'potential_gain_percent' => round((($sr['resistance'][0] - $currentPrice) / $currentPrice) * 100, 2),
            ];
        } else {
            $zones['target_1'] = [
                'price' => round($currentPrice * 1.05, 2),
                'description' => '5% gain - conservative target',
                'potential_gain_percent' => 5.0,
            ];
        }

        // Second target
        if (isset($sr['resistance'][1])) {
            $zones['target_2'] = [
                'price' => $sr['resistance'][1],
                'description' => 'Second resistance - major profit taking',
                'potential_gain_percent' => round((($sr['resistance'][1] - $currentPrice) / $currentPrice) * 100, 2),
            ];
        } else {
            $zones['target_2'] = [
                'price' => round($currentPrice * 1.10, 2),
                'description' => '10% gain - moderate target',
                'potential_gain_percent' => 10.0,
            ];
        }

        // Third target (stretch goal)
        if (isset($sr['resistance'][2])) {
            $zones['target_3'] = [
                'price' => $sr['resistance'][2],
                'description' => 'Strong resistance - exit remaining position',
                'potential_gain_percent' => round((($sr['resistance'][2] - $currentPrice) / $currentPrice) * 100, 2),
            ];
        } else {
            $zones['target_3'] = [
                'price' => round($currentPrice * 1.15, 2),
                'description' => '15% gain - aggressive target',
                'potential_gain_percent' => 15.0,
            ];
        }

        return $zones;
    }

    /**
     * Calculate risk-reward ratio
     */
    private function calculateRiskReward(array $entry, array $exit, float $currentPrice): array
    {
        $analysis = [];

        foreach ($entry as $entryType => $entryData) {
            $entryPrice = $entryData['price'];
            $stopLoss = $entryPrice * 0.95; // 5% stop loss
            $risk = $entryPrice - $stopLoss;

            foreach ($exit as $exitType => $exitData) {
                $reward = $exitData['price'] - $entryPrice;
                $ratio = $risk > 0 ? $reward / $risk : 0;

                $analysis["{$entryType}_to_{$exitType}"] = [
                    'entry' => $entryPrice,
                    'exit' => $exitData['price'],
                    'stop_loss' => round($stopLoss, 2),
                    'risk' => round($risk, 2),
                    'reward' => round($reward, 2),
                    'ratio' => round($ratio, 2),
                    'rating' => $this->rateRiskReward($ratio),
                ];
            }
        }

        return $analysis;
    }

    /**
     * Rate risk-reward ratio
     */
    private function rateRiskReward(float $ratio): string
    {
        if ($ratio >= 3) return 'Excellent';
        if ($ratio >= 2) return 'Good';
        if ($ratio >= 1.5) return 'Fair';
        if ($ratio >= 1) return 'Acceptable';
        return 'Poor';
    }

    /**
     * Get position recommendation based on current price
     */
    private function getPositionRecommendation(float $currentPrice, array $entryZones, array $sr): array
    {
        $nearestSupport = !empty($sr['support']) ? $sr['support'][0] : $currentPrice * 0.95;
        $nearestResistance = !empty($sr['resistance']) ? $sr['resistance'][0] : $currentPrice * 1.05;

        $distanceToSupport = (($currentPrice - $nearestSupport) / $nearestSupport) * 100;
        $distanceToResistance = (($nearestResistance - $currentPrice) / $currentPrice) * 100;

        // Determine current position
        if ($distanceToSupport <= 2) {
            $position = 'NEAR_SUPPORT';
            $action = 'GOOD_BUY_ZONE 🟢';
            $description = "💰 Like a SALE at the store! Stock price is low and near a strong support floor. This is a good time to buy because: (1) Price is cheap right now, (2) It's unlikely to go much lower (strong floor below), (3) Good chance it will go up from here! Think of it like buying your favorite toy when it's on discount.";
            $confidence = 'High';
        } elseif ($distanceToResistance <= 2) {
            $position = 'NEAR_RESISTANCE';
            $action = 'WAIT_OR_SELL 🔴';
            $description = "⚠️ Like when a toy costs TOO MUCH! Stock price is high and hitting a ceiling (resistance). This means: (1) Price is expensive right now, (2) It's hard for the price to go higher (ceiling blocking it), (3) Might come back down. If you own it, consider selling to take your profit. If you don't own it, wait for price to drop.";
            $confidence = 'High';
        } elseif ($distanceToSupport > $distanceToResistance) {
            $position = 'UPPER_RANGE';
            $action = 'WAIT_FOR_PULLBACK 🟡';
            $description = "⏰ Like waiting for a DISCOUNT! Stock price is high right now (not a good deal). Best to WAIT until it goes on sale (price comes down). Imagine your mom saying 'Wait for the sale!' instead of buying when it's full price. Be patient!";
            $confidence = 'Medium';
        } else {
            $position = 'LOWER_RANGE';
            $action = 'CONSIDER_BUY 🟢';
            $description = "🛒 Like a GOOD DEAL starting! Stock price is getting lower (closer to sale price). You can start buying a little bit at a time. Think of it like: Instead of buying 10 toys at once, buy 2 now, 2 later, 2 more later. This way, if price goes down more, you can buy more at even better prices!";
            $confidence = 'Medium-High';
        }

        return [
            'current_position' => $position,
            'recommended_action' => $action,
            'description' => $description,
            'confidence' => $confidence,
            'distance_to_support_percent' => round($distanceToSupport, 2),
            'distance_to_resistance_percent' => round($distanceToResistance, 2),
        ];
    }

    /**
     * Default recommendation when insufficient data
     */
    private function defaultRecommendation(float $currentPrice): array
    {
        return [
            'current_price' => $currentPrice,
            'entry_recommendation' => [
                'conservative' => [
                    'price' => round($currentPrice * 0.95, 2),
                    'description' => 'Insufficient data - conservative 5% below current',
                ],
            ],
            'exit_recommendation' => [
                'target_1' => [
                    'price' => round($currentPrice * 1.10, 2),
                    'description' => 'Insufficient data - 10% above current',
                ],
            ],
            'support_levels' => [],
            'resistance_levels' => [],
            'fibonacci_levels' => [],
            'atr_levels' => [],
            'risk_reward_ratio' => [],
            'position_recommendation' => [
                'current_position' => 'UNKNOWN',
                'recommended_action' => 'WAIT',
                'description' => 'Insufficient historical data for accurate analysis',
                'confidence' => 'Low',
            ],
        ];
    }
}
