<?php

namespace App\Services;

class StockAnalyzer
{
    private array $analysis = [];
    private array $reasons = [];
    private float $score = 0;

    /**
     * Analyze stock data and provide recommendations
     *
     * @param array $stockData
     * @return array
     */
    public function analyze(array $stockData): array
    {
        $this->analysis = [];
        $this->reasons = [];
        $this->score = 0;

        // Perform various analyses
        $this->analyzeFundamentals($stockData);
        $this->analyzeTechnicals($stockData);
        $this->analyzeValuation($stockData);
        $this->analyzeFinancialHealth($stockData);
        $this->analyzeMomentum($stockData);
        $this->analyzeDividend($stockData);

        // Determine recommendation
        $recommendation = $this->getRecommendation($this->score);

        return [
            'symbol' => $stockData['symbol'],
            'name' => $stockData['name'],
            'current_price' => $stockData['current_price'],
            'score' => round($this->score, 2),
            'max_score' => 100,
            'recommendation' => $recommendation,
            'analysis' => $this->analysis,
            'reasons' => $this->reasons,
            'metrics' => $this->getKeyMetrics($stockData),
        ];
    }

    /**
     * Analyze fundamental ratios
     */
    private function analyzeFundamentals(array $data): void
    {
        $category = 'Fundamental Analysis';
        $points = 0;
        $maxPoints = 20;

        // P/E Ratio Analysis (0-8 points)
        if ($data['pe_ratio'] !== null) {
            if ($data['pe_ratio'] > 0 && $data['pe_ratio'] < 15) {
                $points += 8;
                $this->addReason('positive', "P/E Ratio ({$data['pe_ratio']}) is attractive (< 15), indicating potentially undervalued stock.");
            } elseif ($data['pe_ratio'] >= 15 && $data['pe_ratio'] < 25) {
                $points += 5;
                $this->addReason('neutral', "P/E Ratio ({$data['pe_ratio']}) is moderate (15-25), fairly valued.");
            } elseif ($data['pe_ratio'] >= 25) {
                $points += 2;
                $this->addReason('warning', "P/E Ratio ({$data['pe_ratio']}) is high (> 25), stock may be overvalued.");
            } elseif ($data['pe_ratio'] < 0) {
                $points += 0;
                $this->addReason('negative', "Negative P/E Ratio indicates company is not profitable.");
            }
        }

        // P/B Ratio Analysis (0-7 points)
        if ($data['pb_ratio'] !== null) {
            if ($data['pb_ratio'] > 0 && $data['pb_ratio'] < 1.5) {
                $points += 7;
                $this->addReason('positive', "P/B Ratio ({$data['pb_ratio']}) is excellent (< 1.5), trading below book value.");
            } elseif ($data['pb_ratio'] >= 1.5 && $data['pb_ratio'] < 3) {
                $points += 4;
                $this->addReason('neutral', "P/B Ratio ({$data['pb_ratio']}) is reasonable (1.5-3).");
            } else {
                $points += 1;
                $this->addReason('warning', "P/B Ratio ({$data['pb_ratio']}) is high (> 3), stock trading at premium to book value.");
            }
        }

        // EPS Analysis (0-5 points)
        if ($data['eps'] !== null) {
            if ($data['eps'] > 0) {
                $points += 5;
                $this->addReason('positive', "Positive EPS ({$data['eps']}) shows company is profitable.");
            } else {
                $this->addReason('negative', "Negative EPS ({$data['eps']}) indicates losses.");
            }
        }

        $this->analysis[$category] = [
            'score' => round(($points / $maxPoints) * 100, 2),
            'points' => $points,
            'max_points' => $maxPoints,
        ];

        $this->score += ($points / $maxPoints) * 20; // 20% weight
    }

    /**
     * Analyze technical indicators
     */
    private function analyzeTechnicals(array $data): void
    {
        $category = 'Technical Analysis';
        $points = 0;
        $maxPoints = 20;

        $closes = $data['historical_closes'] ?? [];

        if (count($closes) >= 20) {
            // Calculate RSI (0-8 points)
            $rsi = $this->calculateRSI($closes, 14);
            if ($rsi !== null) {
                if ($rsi < 30) {
                    $points += 8;
                    $this->addReason('positive', "RSI ({$rsi}) indicates oversold condition - potential buying opportunity.");
                } elseif ($rsi >= 30 && $rsi < 50) {
                    $points += 6;
                    $this->addReason('neutral', "RSI ({$rsi}) shows neutral to slightly bearish momentum.");
                } elseif ($rsi >= 50 && $rsi <= 70) {
                    $points += 5;
                    $this->addReason('positive', "RSI ({$rsi}) shows healthy bullish momentum.");
                } else {
                    $points += 2;
                    $this->addReason('warning', "RSI ({$rsi}) indicates overbought condition - stock may be overextended.");
                }
            }

            // Moving Averages (0-7 points)
            $currentPrice = $data['current_price'];
            $sma20 = $this->calculateSMA($closes, 20);
            $sma50 = count($closes) >= 50 ? $this->calculateSMA($closes, 50) : null;

            if ($sma20 !== null) {
                if ($currentPrice > $sma20) {
                    $points += 4;
                    $this->addReason('positive', "Price (Rp " . number_format($currentPrice) . ") is above 20-day SMA (Rp " . number_format($sma20) . "), indicating uptrend.");
                } else {
                    $points += 1;
                    $this->addReason('warning', "Price (Rp " . number_format($currentPrice) . ") is below 20-day SMA (Rp " . number_format($sma20) . "), indicating downtrend.");
                }
            }

            if ($sma50 !== null && $sma20 !== null) {
                if ($sma20 > $sma50) {
                    $points += 3;
                    $this->addReason('positive', "20-day SMA crossed above 50-day SMA - bullish signal (Golden Cross potential).");
                }
            }

            // Price Momentum (0-5 points)
            $priceChange = $data['change_percent'];
            if ($priceChange > 5) {
                $points += 3;
                $this->addReason('positive', "Strong positive momentum: +{$priceChange}% today.");
            } elseif ($priceChange > 0) {
                $points += 5;
                $this->addReason('positive', "Positive momentum: +{$priceChange}% today.");
            } elseif ($priceChange < -5) {
                $points += 1;
                $this->addReason('warning', "Sharp decline: {$priceChange}% today - consider waiting for stabilization.");
            }
        }

        $this->analysis[$category] = [
            'score' => round(($points / $maxPoints) * 100, 2),
            'points' => $points,
            'max_points' => $maxPoints,
        ];

        $this->score += ($points / $maxPoints) * 25; // 25% weight
    }

    /**
     * Analyze valuation metrics
     */
    private function analyzeValuation(array $data): void
    {
        $category = 'Valuation';
        $points = 0;
        $maxPoints = 15;

        // Compare current price to target price (0-8 points)
        if ($data['target_price'] !== null && $data['current_price'] > 0) {
            $upside = (($data['target_price'] - $data['current_price']) / $data['current_price']) * 100;

            if ($upside > 20) {
                $points += 8;
                $this->addReason('positive', "Significant upside potential: {$upside}% to analyst target price of Rp " . number_format($data['target_price']) . ".");
            } elseif ($upside > 10) {
                $points += 6;
                $this->addReason('positive', "Good upside potential: {$upside}% to target price.");
            } elseif ($upside > 0) {
                $points += 4;
                $this->addReason('neutral', "Moderate upside: {$upside}% to target price.");
            } else {
                $points += 2;
                $this->addReason('warning', "Limited upside: {$upside}% to target price - stock near or above target.");
            }
        }

        // Analyst recommendation (0-7 points)
        $rec = strtolower($data['recommendation']);
        if (in_array($rec, ['strong_buy', 'buy'])) {
            $points += 7;
            $this->addReason('positive', "Analysts recommend: " . ucfirst(str_replace('_', ' ', $rec)) . ".");
        } elseif ($rec === 'hold') {
            $points += 4;
            $this->addReason('neutral', "Analysts recommend: Hold.");
        } elseif (in_array($rec, ['sell', 'strong_sell'])) {
            $points += 1;
            $this->addReason('negative', "Analysts recommend: " . ucfirst(str_replace('_', ' ', $rec)) . ".");
        }

        $this->analysis[$category] = [
            'score' => round(($points / $maxPoints) * 100, 2),
            'points' => $points,
            'max_points' => $maxPoints,
        ];

        $this->score += ($points / $maxPoints) * 15; // 15% weight
    }

    /**
     * Analyze financial health
     */
    private function analyzeFinancialHealth(array $data): void
    {
        $category = 'Financial Health';
        $points = 0;
        $maxPoints = 20;

        // ROE Analysis (0-7 points)
        if ($data['roe'] !== null) {
            $roePercent = $data['roe'] * 100;
            if ($roePercent > 15) {
                $points += 7;
                $this->addReason('positive', "Excellent ROE: {$roePercent}% (> 15%) shows efficient use of equity.");
            } elseif ($roePercent > 10) {
                $points += 5;
                $this->addReason('positive', "Good ROE: {$roePercent}% (> 10%).");
            } elseif ($roePercent > 0) {
                $points += 3;
                $this->addReason('neutral', "Moderate ROE: {$roePercent}%.");
            } else {
                $points += 0;
                $this->addReason('negative', "Negative ROE: {$roePercent}% indicates poor returns.");
            }
        }

        // Profit Margin (0-7 points)
        if ($data['profit_margin'] !== null) {
            $marginPercent = $data['profit_margin'] * 100;
            if ($marginPercent > 20) {
                $points += 7;
                $this->addReason('positive', "Excellent profit margin: {$marginPercent}% (> 20%).");
            } elseif ($marginPercent > 10) {
                $points += 5;
                $this->addReason('positive', "Healthy profit margin: {$marginPercent}%.");
            } elseif ($marginPercent > 0) {
                $points += 3;
                $this->addReason('neutral', "Modest profit margin: {$marginPercent}%.");
            } else {
                $points += 0;
                $this->addReason('negative', "Negative profit margin: {$marginPercent}% - company is losing money.");
            }
        }

        // Debt to Equity (0-6 points)
        if ($data['debt_to_equity'] !== null) {
            $de = $data['debt_to_equity'];
            if ($de < 50) {
                $points += 6;
                $this->addReason('positive', "Low debt-to-equity ratio: {$de}% - conservative balance sheet.");
            } elseif ($de < 100) {
                $points += 4;
                $this->addReason('neutral', "Moderate debt-to-equity: {$de}%.");
            } else {
                $points += 2;
                $this->addReason('warning', "High debt-to-equity: {$de}% - higher financial risk.");
            }
        }

        $this->analysis[$category] = [
            'score' => round(($points / $maxPoints) * 100, 2),
            'points' => $points,
            'max_points' => $maxPoints,
        ];

        $this->score += ($points / $maxPoints) * 20; // 20% weight
    }

    /**
     * Analyze momentum and volume
     */
    private function analyzeMomentum(array $data): void
    {
        $category = 'Momentum & Liquidity';
        $points = 0;
        $maxPoints = 10;

        // Volume analysis (0-5 points)
        if ($data['volume'] > 0 && $data['avg_volume'] > 0) {
            $volumeRatio = $data['volume'] / $data['avg_volume'];

            if ($volumeRatio > 1.5 && $data['change_percent'] > 0) {
                $points += 5;
                $this->addReason('positive', "High volume (" . number_format($data['volume']) . ") with price increase - strong buying interest.");
            } elseif ($volumeRatio > 1.2) {
                $points += 3;
                $this->addReason('neutral', "Above average volume indicates active trading.");
            } elseif ($volumeRatio < 0.5) {
                $points += 2;
                $this->addReason('warning', "Low volume - liquidity concerns, harder to enter/exit positions.");
            } else {
                $points += 3;
            }
        }

        // Market cap considerations (0-5 points)
        if ($data['market_cap'] > 0) {
            $marketCapT = $data['market_cap'] / 1_000_000_000_000; // in trillion IDR

            if ($marketCapT > 10) {
                $points += 5;
                $this->addReason('positive', "Large-cap stock (Rp " . number_format($marketCapT, 2) . "T) - typically more stable and liquid.");
            } elseif ($marketCapT > 1) {
                $points += 4;
                $this->addReason('neutral', "Mid-cap stock (Rp " . number_format($marketCapT, 2) . "T) - balance of growth and stability.");
            } else {
                $points += 3;
                $this->addReason('warning', "Small-cap stock (Rp " . number_format($marketCapT, 2) . "T) - higher volatility and risk.");
            }
        }

        $this->analysis[$category] = [
            'score' => round(($points / $maxPoints) * 100, 2),
            'points' => $points,
            'max_points' => $maxPoints,
        ];

        $this->score += ($points / $maxPoints) * 10; // 10% weight
    }

    /**
     * Analyze dividend yield
     */
    private function analyzeDividend(array $data): void
    {
        $category = 'Dividend';
        $points = 0;
        $maxPoints = 10;

        if ($data['dividend_yield'] > 0) {
            $yieldPercent = $data['dividend_yield'] * 100;

            if ($yieldPercent > 5) {
                $points += 10;
                $this->addReason('positive', "Excellent dividend yield: {$yieldPercent}% (> 5%) - great for income investors.");
            } elseif ($yieldPercent > 3) {
                $points += 8;
                $this->addReason('positive', "Good dividend yield: {$yieldPercent}% (> 3%).");
            } elseif ($yieldPercent > 1) {
                $points += 5;
                $this->addReason('neutral', "Moderate dividend yield: {$yieldPercent}%.");
            } else {
                $points += 3;
                $this->addReason('neutral', "Low dividend yield: {$yieldPercent}% - growth focus over income.");
            }
        } else {
            $points += 2;
            $this->addReason('neutral', "No dividend paid - company reinvesting in growth.");
        }

        $this->analysis[$category] = [
            'score' => round(($points / $maxPoints) * 100, 2),
            'points' => $points,
            'max_points' => $maxPoints,
        ];

        $this->score += ($points / $maxPoints) * 10; // 10% weight
    }

    /**
     * Calculate RSI (Relative Strength Index)
     */
    private function calculateRSI(array $closes, int $period = 14): ?float
    {
        if (count($closes) < $period + 1) {
            return null;
        }

        $gains = [];
        $losses = [];

        for ($i = 1; $i < count($closes); $i++) {
            $change = $closes[$i] - $closes[$i - 1];
            $gains[] = $change > 0 ? $change : 0;
            $losses[] = $change < 0 ? abs($change) : 0;
        }

        $avgGain = array_sum(array_slice($gains, -$period)) / $period;
        $avgLoss = array_sum(array_slice($losses, -$period)) / $period;

        if ($avgLoss == 0) {
            return 100;
        }

        $rs = $avgGain / $avgLoss;
        $rsi = 100 - (100 / (1 + $rs));

        return round($rsi, 2);
    }

    /**
     * Calculate Simple Moving Average
     */
    private function calculateSMA(array $closes, int $period): ?float
    {
        if (count($closes) < $period) {
            return null;
        }

        $slice = array_slice($closes, -$period);
        return array_sum($slice) / $period;
    }

    /**
     * Add reason to the list
     */
    private function addReason(string $type, string $message): void
    {
        $this->reasons[] = [
            'type' => $type, // positive, negative, neutral, warning
            'message' => $message,
        ];
    }

    /**
     * Get recommendation based on score
     */
    private function getRecommendation(float $score): array
    {
        if ($score >= 80) {
            return [
                'action' => 'STRONG BUY',
                'confidence' => 'High',
                'color' => 'success',
                'description' => 'Excellent opportunity. Multiple positive indicators suggest strong potential.',
            ];
        } elseif ($score >= 65) {
            return [
                'action' => 'BUY',
                'confidence' => 'Medium-High',
                'color' => 'success',
                'description' => 'Good opportunity. Several positive factors support buying.',
            ];
        } elseif ($score >= 50) {
            return [
                'action' => 'HOLD',
                'confidence' => 'Medium',
                'color' => 'warning',
                'description' => 'Mixed signals. Consider holding or waiting for better entry point.',
            ];
        } elseif ($score >= 35) {
            return [
                'action' => 'CONSIDER SELLING',
                'confidence' => 'Medium',
                'color' => 'warning',
                'description' => 'Several concerning factors. Evaluate your position carefully.',
            ];
        } else {
            return [
                'action' => 'SELL',
                'confidence' => 'High',
                'color' => 'danger',
                'description' => 'Multiple negative indicators. Consider exiting position.',
            ];
        }
    }

    /**
     * Get key metrics summary
     */
    private function getKeyMetrics(array $data): array
    {
        $closes = $data['historical_closes'] ?? [];

        // Calculate technical indicators
        $rsi = null;
        $aboveSma = false;

        if (count($closes) >= 20) {
            $rsi = $this->calculateRSI($closes, 14);
            $sma20 = $this->calculateSMA($closes, 20);
            $aboveSma = $data['current_price'] > $sma20;
        }

        return [
            'price' => [
                'current' => $data['current_price'],
                'change' => $data['change'],
                'change_percent' => round($data['change_percent'], 2),
                'day_range' => [$data['day_low'], $data['day_high']],
            ],
            'valuation' => [
                'pe_ratio' => $data['pe_ratio'],
                'pb_ratio' => $data['pb_ratio'],
                'market_cap' => $data['market_cap'],
            ],
            'profitability' => [
                'eps' => $data['eps'],
                'roe' => $data['roe'] ? round($data['roe'] * 100, 2) : null,
                'profit_margin' => $data['profit_margin'] ? round($data['profit_margin'] * 100, 2) : null,
            ],
            'dividend' => [
                'yield' => $data['dividend_yield'] ? round($data['dividend_yield'] * 100, 2) : 0,
                'rate' => $data['dividend_rate'],
            ],
            'technical' => [
                'rsi' => $rsi,
                'above_sma' => $aboveSma,
            ],
        ];
    }

    /**
     * Analyze and rank multiple stocks
     */
    public function rankStocks(array $stocksData): array
    {
        $analyses = [];

        foreach ($stocksData as $stockData) {
            $analyses[] = $this->analyze($stockData);
        }

        // Sort by score descending
        usort($analyses, function ($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        return $analyses;
    }
}
