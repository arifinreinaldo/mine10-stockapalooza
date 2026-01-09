<?php

namespace App\Services;

use App\Data\IndonesianMarketData;

class StockAnalyzer
{
    private array $analysis = [];
    private array $reasons = [];
    private float $score = 0;
    private string $currency = 'IDR';
    private string $currencySymbol = 'Rp';
    private bool $hasFundamentals = false;
    private array $weights = [];

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

        // Set currency from stock data
        $this->currency = $stockData['currency'] ?? 'IDR';
        $this->currencySymbol = $this->getCurrencySymbol($this->currency);

        // Detect if fundamental data is available
        $this->hasFundamentals = $this->detectFundamentals($stockData);

        // Calculate adaptive weights based on data availability
        $this->weights = $this->calculateAdaptiveWeights();

        // Add notice if using technical-only analysis
        if (!$this->hasFundamentals) {
            $this->addReason('info', 'Using technical analysis mode: Fundamental data unavailable. Scoring based on price action, momentum, and technical indicators.');
        }

        // Perform various analyses
        $this->analyzeFundamentals($stockData);
        $this->analyzeTechnicals($stockData);
        $this->analyzeValuation($stockData);
        $this->analyzeFinancialHealth($stockData);
        $this->analyzeMomentum($stockData);
        $this->analyzeDividend($stockData);

        // Phase 1 Enhancements
        $this->analyzeRiskMetrics($stockData);
        $this->analyzeForeignFlow($stockData);

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
            'scoring_mode' => $this->hasFundamentals ? 'hybrid' : 'technical',
        ];
    }

    /**
     * Detect if fundamental data is available
     */
    private function detectFundamentals(array $data): bool
    {
        // Check if we have at least 2 key fundamental metrics
        $fundamentalFields = [
            $data['pe_ratio'] ?? null,
            $data['pb_ratio'] ?? null,
            $data['eps'] ?? null,
            $data['roe'] ?? null,
            $data['market_cap'] ?? 0,
        ];

        $availableCount = 0;
        foreach ($fundamentalFields as $field) {
            if ($field !== null && $field != 0) {
                $availableCount++;
            }
        }

        // If we have at least 2 fundamental metrics, consider fundamentals available
        return $availableCount >= 2;
    }

    /**
     * Calculate adaptive scoring weights based on data availability
     * Updated for Phase 1 with Risk Assessment and Market Participation
     */
    private function calculateAdaptiveWeights(): array
    {
        if ($this->hasFundamentals) {
            // Standard weights when fundamentals are available
            // Total: 100% across 8 categories
            return [
                'fundamentals' => 15,      // Reduced from 20
                'technicals' => 25,        // Keep same (ADX added internally)
                'valuation' => 12,         // Reduced from 15
                'financial_health' => 15,  // Reduced from 20
                'momentum' => 8,           // Reduced from 10
                'dividend' => 5,           // Reduced from 10
                'risk_metrics' => 12,      // NEW - Phase 1
                'foreign_flow' => 8,       // NEW - Phase 1
            ];
        } else {
            // Adaptive weights when fundamentals are missing
            // Emphasize technical analysis and momentum
            return [
                'fundamentals' => 0,
                'technicals' => 40,        // Boost for technical-only mode
                'valuation' => 0,
                'financial_health' => 0,
                'momentum' => 25,          // Boost
                'dividend' => 10,
                'risk_metrics' => 15,      // NEW - Phase 1 (important even without fundamentals)
                'foreign_flow' => 10,      // NEW - Phase 1
            ];
        }
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

        $weight = $this->weights['fundamentals'] ?? 20;
        $this->score += ($points / $maxPoints) * $weight;
    }

    /**
     * Analyze technical indicators
     * Updated for Phase 1 with ADX
     */
    private function analyzeTechnicals(array $data): void
    {
        $category = 'Technical Analysis';
        $points = 0;
        $maxPoints = 25; // Increased from 20 to add ADX (5 points)

        $closes = $data['historical_closes'] ?? [];
        $highs = $data['historical_highs'] ?? [];
        $lows = $data['historical_lows'] ?? [];

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
                    $this->addReason('positive', "Price ({$this->currencySymbol} " . number_format($currentPrice) . ") is above 20-day SMA ({$this->currencySymbol} " . number_format($sma20) . "), indicating uptrend.");
                } else {
                    $points += 1;
                    $this->addReason('warning', "Price ({$this->currencySymbol} " . number_format($currentPrice) . ") is below 20-day SMA ({$this->currencySymbol} " . number_format($sma20) . "), indicating downtrend.");
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

            // ADX - Trend Strength (0-5 points) - Phase 1 Enhancement
            if (count($highs) >= 20 && count($lows) >= 20) {
                $adx = $this->calculateADX($highs, $lows, $closes);

                if ($adx !== null) {
                    $adxValue = $adx['adx'];
                    $signal = $adx['signal'];

                    if ($signal === 'STRONG_UPTREND') {
                        $points += 5;
                        $this->addReason('positive', "ADX ({$adxValue}) shows strong uptrend - high confidence trend confirmed.");
                    } elseif ($signal === 'WEAK_UPTREND') {
                        $points += 3;
                        $this->addReason('neutral', "ADX ({$adxValue}) shows weak uptrend - trend not fully confirmed.");
                    } elseif ($signal === 'NO_TREND') {
                        $points += 1;
                        $this->addReason('warning', "ADX ({$adxValue}) shows no clear trend - avoid trading until trend develops.");
                    } elseif ($signal === 'STRONG_DOWNTREND') {
                        $points += 0;
                        $this->addReason('negative', "ADX ({$adxValue}) confirms strong downtrend - bearish signal.");
                    } elseif ($signal === 'WEAK_DOWNTREND') {
                        $points += 1;
                        $this->addReason('warning', "ADX ({$adxValue}) shows weak downtrend - caution advised.");
                    }
                }
            }
        }

        $this->analysis[$category] = [
            'score' => round(($points / $maxPoints) * 100, 2),
            'points' => $points,
            'max_points' => $maxPoints,
        ];

        $weight = $this->weights['technicals'] ?? 25;
        $this->score += ($points / $maxPoints) * $weight;
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
                $this->addReason('positive', "Significant upside potential: {$upside}% to analyst target price of {$this->currencySymbol} " . number_format($data['target_price']) . ".");
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

        $weight = $this->weights['valuation'] ?? 15;
        $this->score += ($points / $maxPoints) * $weight;
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

        $weight = $this->weights['financial_health'] ?? 20;
        $this->score += ($points / $maxPoints) * $weight;
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
            $capInfo = $this->getMarketCapDivisor($this->currency);
            $marketCapValue = $data['market_cap'] / $capInfo['divisor'];

            if ($marketCapValue > 10) {
                $points += 5;
                $this->addReason('positive', "Large-cap stock ({$this->currencySymbol} " . number_format($marketCapValue, 2) . "{$capInfo['unit']}) - typically more stable and liquid.");
            } elseif ($marketCapValue > 1) {
                $points += 4;
                $this->addReason('neutral', "Mid-cap stock ({$this->currencySymbol} " . number_format($marketCapValue, 2) . "{$capInfo['unit']}) - balance of growth and stability.");
            } else {
                $points += 3;
                $this->addReason('warning', "Small-cap stock ({$this->currencySymbol} " . number_format($marketCapValue, 2) . "{$capInfo['unit']}) - higher volatility and risk.");
            }
        }

        $this->analysis[$category] = [
            'score' => round(($points / $maxPoints) * 100, 2),
            'points' => $points,
            'max_points' => $maxPoints,
        ];

        $weight = $this->weights['momentum'] ?? 10;
        $this->score += ($points / $maxPoints) * $weight;
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

        $weight = $this->weights['dividend'] ?? 10;
        $this->score += ($points / $maxPoints) * $weight;
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
        $highs = $data['historical_highs'] ?? [];
        $lows = $data['historical_lows'] ?? [];
        $volumes = $data['historical_volumes'] ?? [];

        // Calculate basic technical indicators
        $rsi = null;
        $aboveSma = false;
        $macd = null;
        $stochastic = null;
        $mfi = null;
        $divergence = null;
        $week52 = null;

        if (count($closes) >= 20) {
            $rsi = $this->calculateRSI($closes, 14);
            $sma20 = $this->calculateSMA($closes, 20);
            $aboveSma = $data['current_price'] > $sma20;

            // Calculate 52-week context
            $week52 = $this->calculate52WeekContext($data['current_price'], $closes);
        }

        // Calculate professional indicators
        if (count($closes) >= 26) {
            $macd = $this->calculateMACD($closes);
        }

        if (count($closes) >= 14 && count($highs) >= 14 && count($lows) >= 14) {
            $stochastic = $this->calculateStochastic($closes, $highs, $lows, 14);
        }

        if (count($closes) >= 15 && count($volumes) >= 15 && count($highs) >= 15 && count($lows) >= 15) {
            $mfi = $this->calculateMFI($closes, $highs, $lows, $volumes, 14);
        }

        // Detect divergence (needs RSI history)
        if ($rsi && count($closes) >= 20) {
            $rsiHistory = [];
            for ($i = 0; $i < 20; $i++) {
                $recentCloses = array_slice($closes, 0, -$i ?: count($closes));
                if (count($recentCloses) >= 14) {
                    $rsiHistory[] = $this->calculateRSI($recentCloses, 14);
                }
            }
            $rsiHistory = array_reverse($rsiHistory);
            if (count($rsiHistory) >= 20) {
                $divergence = $this->detectDivergence($closes, $rsiHistory);
            }
        }

        // Phase 1 indicators
        $adx = null;
        $atr = null;
        if (count($closes) >= 20 && count($highs) >= 20 && count($lows) >= 20) {
            $adx = $this->calculateADX($highs, $lows, $closes);
            $atr = $this->calculateATR($highs, $lows, $closes);
        }

        // Phase 2 indicators (contextual)
        $liquidityScore = $this->calculateLiquidityScore($data);
        $shariaCompliance = $this->analyzeShariaCompliance($data['symbol'], $data);
        $bumnStatus = $this->analyzeBUMNStatus($data['symbol']);
        $sectorRotation = $this->analyzeSectorRotation($data['symbol'], $data);

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
                'debt_to_equity' => $data['debt_to_equity'],
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
                'macd' => $macd,
                'stochastic' => $stochastic,
                'mfi' => $mfi,
                'divergence' => $divergence,
                '52_week' => $week52,
                'adx' => $adx,  // Phase 1
                'atr' => $atr,  // Phase 1
            ],
            'ownership' => [
                'institutional_percent' => ($data['held_percent_institutions'] ?? 0) * 100,
                'insider_percent' => ($data['held_percent_insiders'] ?? 0) * 100,
            ],
            // Phase 2: Indonesian Market Context
            'market_context' => [
                'liquidity' => $liquidityScore,
                'sharia_compliance' => $shariaCompliance,
                'bumn_status' => $bumnStatus,
                'sector_rotation' => $sectorRotation,
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

    /**
     * Calculate EMA (Exponential Moving Average)
     * Used for MACD and other indicators
     */
    private function calculateEMA(array $closes, int $period): ?float
    {
        if (count($closes) < $period) {
            return null;
        }

        $multiplier = 2 / ($period + 1);
        $ema = array_sum(array_slice($closes, 0, $period)) / $period; // Start with SMA

        for ($i = $period; $i < count($closes); $i++) {
            $ema = ($closes[$i] - $ema) * $multiplier + $ema;
        }

        return $ema;
    }

    /**
     * Calculate MACD (Moving Average Convergence Divergence)
     * Pro indicator: Shows momentum and trend changes
     *
     * Returns: [macd_line, signal_line, histogram, signal]
     */
    private function calculateMACD(array $closes): ?array
    {
        if (count($closes) < 26) {
            return null;
        }

        // Standard MACD parameters
        $ema12 = $this->calculateEMA($closes, 12);
        $ema26 = $this->calculateEMA($closes, 26);

        if ($ema12 === null || $ema26 === null) {
            return null;
        }

        $macdLine = $ema12 - $ema26;

        // For signal line, we need MACD history (simplified approach)
        // In production, calculate EMA of MACD line
        $signalLine = $macdLine * 0.9; // Simplified

        $histogram = $macdLine - $signalLine;

        // Determine signal
        $signal = 'NEUTRAL';
        if ($histogram > 0 && $macdLine > 0) {
            $signal = 'BULLISH';
        } elseif ($histogram < 0 && $macdLine < 0) {
            $signal = 'BEARISH';
        } elseif ($histogram > 0 && $macdLine < 0) {
            $signal = 'TURNING_UP';
        } elseif ($histogram < 0 && $macdLine > 0) {
            $signal = 'TURNING_DOWN';
        }

        return [
            'macd_line' => round($macdLine, 2),
            'signal_line' => round($signalLine, 2),
            'histogram' => round($histogram, 2),
            'signal' => $signal,
            'interpretation' => $this->interpretMACD($signal, $histogram),
        ];
    }

    private function interpretMACD(string $signal, float $histogram): string
    {
        switch ($signal) {
            case 'BULLISH':
                return 'Strong uptrend - MACD above zero and rising';
            case 'BEARISH':
                return 'Strong downtrend - MACD below zero and falling';
            case 'TURNING_UP':
                return 'Possible reversal UP - histogram turning positive';
            case 'TURNING_DOWN':
                return 'Possible reversal DOWN - histogram turning negative';
            default:
                return 'Neutral - no clear trend';
        }
    }

    /**
     * Calculate Stochastic Oscillator
     * Pro indicator: Overbought/oversold detector (better than RSI for timing)
     *
     * Returns: [%K, %D, signal]
     */
    private function calculateStochastic(array $closes, array $highs, array $lows, int $period = 14): ?array
    {
        if (count($closes) < $period || count($highs) < $period || count($lows) < $period) {
            return null;
        }

        // Get recent data
        $recentCloses = array_slice($closes, -$period);
        $recentHighs = array_slice($highs, -$period);
        $recentLows = array_slice($lows, -$period);

        $currentClose = end($recentCloses);
        $highestHigh = max($recentHighs);
        $lowestLow = min($recentLows);

        // Calculate %K (fast stochastic)
        if ($highestHigh == $lowestLow) {
            $k = 50;
        } else {
            $k = (($currentClose - $lowestLow) / ($highestHigh - $lowestLow)) * 100;
        }

        // %D is typically a 3-period SMA of %K (simplified here)
        $d = $k * 0.9; // Simplified

        // Determine signal
        $signal = 'NEUTRAL';
        if ($k > 80) {
            $signal = 'OVERBOUGHT';
        } elseif ($k < 20) {
            $signal = 'OVERSOLD';
        } elseif ($k > $d && $k < 50) {
            $signal = 'BULLISH_CROSS';
        } elseif ($k < $d && $k > 50) {
            $signal = 'BEARISH_CROSS';
        }

        return [
            'k' => round($k, 2),
            'd' => round($d, 2),
            'signal' => $signal,
            'interpretation' => $this->interpretStochastic($signal, $k),
        ];
    }

    private function interpretStochastic(string $signal, float $k): string
    {
        switch ($signal) {
            case 'OVERBOUGHT':
                return 'Overbought (' . round($k) . ' > 80) - potential sell signal';
            case 'OVERSOLD':
                return 'Oversold (' . round($k) . ' < 20) - potential buy signal';
            case 'BULLISH_CROSS':
                return 'Bullish crossover - %K crossed above %D (buy signal)';
            case 'BEARISH_CROSS':
                return 'Bearish crossover - %K crossed below %D (sell signal)';
            default:
                return 'Neutral zone (20-80) - no extreme signal';
        }
    }

    /**
     * Calculate Money Flow Index (MFI)
     * Pro indicator: Volume-weighted RSI, better for institutional tracking
     *
     * Returns: [mfi, signal, interpretation]
     */
    private function calculateMFI(array $closes, array $highs, array $lows, array $volumes, int $period = 14): ?array
    {
        if (count($closes) < $period + 1) {
            return null;
        }

        $positiveFlow = 0;
        $negativeFlow = 0;

        for ($i = count($closes) - $period; $i < count($closes); $i++) {
            if ($i == 0) continue;

            $typicalPrice = ($highs[$i] + $lows[$i] + $closes[$i]) / 3;
            $prevTypicalPrice = ($highs[$i - 1] + $lows[$i - 1] + $closes[$i - 1]) / 3;
            $moneyFlow = $typicalPrice * $volumes[$i];

            if ($typicalPrice > $prevTypicalPrice) {
                $positiveFlow += $moneyFlow;
            } elseif ($typicalPrice < $prevTypicalPrice) {
                $negativeFlow += $moneyFlow;
            }
        }

        if ($negativeFlow == 0) {
            $mfi = 100;
        } else {
            $moneyRatio = $positiveFlow / $negativeFlow;
            $mfi = 100 - (100 / (1 + $moneyRatio));
        }

        // Determine signal
        $signal = 'NEUTRAL';
        if ($mfi > 80) {
            $signal = 'OVERBOUGHT';
        } elseif ($mfi < 20) {
            $signal = 'OVERSOLD';
        } elseif ($mfi > 50) {
            $signal = 'BULLISH';
        } else {
            $signal = 'BEARISH';
        }

        return [
            'mfi' => round($mfi, 2),
            'signal' => $signal,
            'interpretation' => $this->interpretMFI($signal, $mfi),
        ];
    }

    private function interpretMFI(string $signal, float $mfi): string
    {
        switch ($signal) {
            case 'OVERBOUGHT':
                return 'Overbought (' . round($mfi) . ' > 80) - heavy buying, possible reversal';
            case 'OVERSOLD':
                return 'Oversold (' . round($mfi) . ' < 20) - heavy selling, possible bounce';
            case 'BULLISH':
                return 'Bullish (' . round($mfi) . ' > 50) - money flowing IN';
            case 'BEARISH':
                return 'Bearish (' . round($mfi) . ' < 50) - money flowing OUT';
            default:
                return 'Neutral money flow';
        }
    }

    /**
     * Detect RSI Divergence
     * Pro signal: When price and RSI move in opposite directions
     *
     * Returns: [divergence_type, signal]
     */
    private function detectDivergence(array $closes, array $rsiValues): ?array
    {
        if (count($closes) < 20 || count($rsiValues) < 20) {
            return null;
        }

        // Get recent data
        $recentCloses = array_slice($closes, -20);
        $recentRSI = array_slice($rsiValues, -20);

        // Price trend (last 20 days)
        $priceStart = $recentCloses[0];
        $priceEnd = end($recentCloses);
        $priceTrend = $priceEnd > $priceStart ? 'UP' : 'DOWN';

        // RSI trend
        $rsiStart = $recentRSI[0];
        $rsiEnd = end($recentRSI);
        $rsiTrend = $rsiEnd > $rsiStart ? 'UP' : 'DOWN';

        // Detect divergence
        $divergence = 'NONE';
        $signal = 'NEUTRAL';

        if ($priceTrend == 'DOWN' && $rsiTrend == 'UP') {
            $divergence = 'BULLISH';
            $signal = 'BUY';
        } elseif ($priceTrend == 'UP' && $rsiTrend == 'DOWN') {
            $divergence = 'BEARISH';
            $signal = 'SELL';
        }

        return [
            'divergence' => $divergence,
            'signal' => $signal,
            'interpretation' => $this->interpretDivergence($divergence),
        ];
    }

    private function interpretDivergence(string $divergence): string
    {
        switch ($divergence) {
            case 'BULLISH':
                return 'BULLISH DIVERGENCE: Price falling but RSI rising - reversal UP likely!';
            case 'BEARISH':
                return 'BEARISH DIVERGENCE: Price rising but RSI falling - reversal DOWN likely!';
            default:
                return 'No divergence detected';
        }
    }

    /**
     * Calculate 52-week high/low context
     * Pro insight: Where is current price relative to yearly range?
     */
    private function calculate52WeekContext(float $currentPrice, array $historicalCloses): array
    {
        if (count($historicalCloses) < 60) {
            // Use available data
            $high52w = max($historicalCloses);
            $low52w = min($historicalCloses);
        } else {
            $high52w = max($historicalCloses);
            $low52w = min($historicalCloses);
        }

        $range = $high52w - $low52w;
        if ($range == 0) {
            $percentInRange = 50;
        } else {
            $percentInRange = (($currentPrice - $low52w) / $range) * 100;
        }

        // Prevent division by zero
        $distanceFromHigh = $currentPrice > 0 ? (($high52w - $currentPrice) / $currentPrice) * 100 : 0;
        $distanceFromLow = $currentPrice > 0 ? (($currentPrice - $low52w) / $currentPrice) * 100 : 0;

        // Determine position
        $position = 'MIDDLE';
        if ($percentInRange > 90) {
            $position = 'NEAR_HIGH';
        } elseif ($percentInRange > 75) {
            $position = 'UPPER_RANGE';
        } elseif ($percentInRange < 10) {
            $position = 'NEAR_LOW';
        } elseif ($percentInRange < 25) {
            $position = 'LOWER_RANGE';
        }

        return [
            'high_52w' => $high52w,
            'low_52w' => $low52w,
            'current_price' => $currentPrice,
            'percent_in_range' => round($percentInRange, 1),
            'distance_from_high_percent' => round($distanceFromHigh, 1),
            'distance_from_low_percent' => round($distanceFromLow, 1),
            'position' => $position,
            'interpretation' => $this->interpret52WeekPosition($position, $percentInRange),
        ];
    }

    private function interpret52WeekPosition(string $position, float $percent): string
    {
        switch ($position) {
            case 'NEAR_HIGH':
                return 'Near 52-week HIGH (' . round($percent) . '%) - strong momentum or overbought';
            case 'UPPER_RANGE':
                return 'In upper range (' . round($percent) . '%) - bullish territory';
            case 'NEAR_LOW':
                return 'Near 52-week LOW (' . round($percent) . '%) - potential bargain or falling knife';
            case 'LOWER_RANGE':
                return 'In lower range (' . round($percent) . '%) - possible value opportunity';
            default:
                return 'In middle range (' . round($percent) . '%) - neutral zone';
        }
    }

    /**
     * Get currency symbol from currency code
     */
    private function getCurrencySymbol(string $currency): string
    {
        return match(strtoupper($currency)) {
            'IDR' => 'Rp',
            'SGD' => 'S$',
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'JPY' => '¥',
            'CNY' => '¥',
            'HKD' => 'HK$',
            'MYR' => 'RM',
            'THB' => '฿',
            default => $currency . ' '
        };
    }

    /**
     * Get market cap divisor based on currency
     * Indonesian Rupiah uses trillion, most others use billion
     */
    private function getMarketCapDivisor(string $currency): array
    {
        return match(strtoupper($currency)) {
            'IDR' => ['divisor' => 1_000_000_000_000, 'unit' => 'T'],
            default => ['divisor' => 1_000_000_000, 'unit' => 'B']
        };
    }

    // ========================================================================
    // PHASE 1 INDICATORS: ADX, ATR, Foreign Flow
    // ========================================================================

    /**
     * Calculate ADX (Average Directional Index)
     * Measures trend strength (0-100)
     * Phase 1 Enhancement
     */
    private function calculateADX(array $highs, array $lows, array $closes, int $period = 14): ?array
    {
        if (count($highs) < $period + 1 || count($lows) < $period + 1 || count($closes) < $period + 1) {
            return null;
        }

        $trueRanges = [];
        $plusDM = [];
        $minusDM = [];

        // Calculate True Range, +DM, -DM
        for ($i = 1; $i < count($closes); $i++) {
            // Skip days with missing or invalid data
            if (empty($highs[$i]) || empty($lows[$i]) || empty($closes[$i]) ||
                empty($highs[$i - 1]) || empty($lows[$i - 1]) || empty($closes[$i - 1])) {
                continue;
            }

            // Skip if values are zero
            if ($highs[$i] <= 0 || $lows[$i] <= 0 || $closes[$i] <= 0 ||
                $highs[$i - 1] <= 0 || $lows[$i - 1] <= 0 || $closes[$i - 1] <= 0) {
                continue;
            }

            // True Range
            $tr1 = $highs[$i] - $lows[$i];
            $tr2 = abs($highs[$i] - $closes[$i - 1]);
            $tr3 = abs($lows[$i] - $closes[$i - 1]);
            $trueRanges[] = max($tr1, $tr2, $tr3);

            // +DM and -DM
            $highDiff = $highs[$i] - $highs[$i - 1];
            $lowDiff = $lows[$i - 1] - $lows[$i];

            if ($highDiff > $lowDiff && $highDiff > 0) {
                $plusDM[] = $highDiff;
                $minusDM[] = 0;
            } elseif ($lowDiff > $highDiff && $lowDiff > 0) {
                $plusDM[] = 0;
                $minusDM[] = $lowDiff;
            } else {
                $plusDM[] = 0;
                $minusDM[] = 0;
            }
        }

        // Need enough valid data points
        if (count($trueRanges) < $period) {
            return null;
        }

        // Smooth with period average
        $smoothTR = array_sum(array_slice($trueRanges, -$period)) / $period;
        $smoothPlusDM = array_sum(array_slice($plusDM, -$period)) / $period;
        $smoothMinusDM = array_sum(array_slice($minusDM, -$period)) / $period;

        // Calculate +DI and -DI
        $plusDI = $smoothTR > 0 ? ($smoothPlusDM / $smoothTR) * 100 : 0;
        $minusDI = $smoothTR > 0 ? ($smoothMinusDM / $smoothTR) * 100 : 0;

        // Calculate DX
        $diSum = $plusDI + $minusDI;
        $dx = $diSum > 0 ? (abs($plusDI - $minusDI) / $diSum) * 100 : 0;

        // ADX is smoothed DX (simplified - using direct DX for now)
        $adx = $dx;

        return [
            'adx' => round($adx, 2),
            'plus_di' => round($plusDI, 2),
            'minus_di' => round($minusDI, 2),
            'trend_strength' => $this->getADXStrength($adx),
            'signal' => $this->getADXSignal($adx, $plusDI, $minusDI),
        ];
    }

    /**
     * Get ADX trend strength category
     */
    private function getADXStrength(float $adx): string
    {
        if ($adx > 50) return 'Very Strong';
        if ($adx > 25) return 'Strong';
        if ($adx > 20) return 'Moderate';
        return 'Weak/No Trend';
    }

    /**
     * Get ADX trading signal
     */
    private function getADXSignal(float $adx, float $plusDI, float $minusDI): string
    {
        if ($adx < 20) {
            return 'NO_TREND'; // Avoid trading
        }

        if ($plusDI > $minusDI) {
            return $adx > 25 ? 'STRONG_UPTREND' : 'WEAK_UPTREND';
        } else {
            return $adx > 25 ? 'STRONG_DOWNTREND' : 'WEAK_DOWNTREND';
        }
    }

    /**
     * Calculate ATR (Average True Range)
     * Measures volatility
     * Phase 1 Enhancement
     */
    private function calculateATR(array $highs, array $lows, array $closes, int $period = 14): ?array
    {
        if (count($highs) < $period + 1 || count($lows) < $period + 1 || count($closes) < $period + 1) {
            return null;
        }

        $trueRanges = [];

        for ($i = 1; $i < count($closes); $i++) {
            // Skip days with missing or invalid data (e.g., weekends, holidays)
            if (empty($highs[$i]) || empty($lows[$i]) || empty($closes[$i]) || empty($closes[$i - 1])) {
                continue;
            }

            // Skip if values are zero (invalid data)
            if ($highs[$i] <= 0 || $lows[$i] <= 0 || $closes[$i] <= 0 || $closes[$i - 1] <= 0) {
                continue;
            }

            $tr1 = $highs[$i] - $lows[$i];
            $tr2 = abs($highs[$i] - $closes[$i - 1]);
            $tr3 = abs($lows[$i] - $closes[$i - 1]);
            $trueRanges[] = max($tr1, $tr2, $tr3);
        }

        // Need enough valid data points
        if (count($trueRanges) < $period) {
            return null;
        }

        $atr = array_sum(array_slice($trueRanges, -$period)) / $period;

        // Get the most recent valid close price
        $currentPrice = 0;
        for ($i = count($closes) - 1; $i >= 0; $i--) {
            if (!empty($closes[$i]) && $closes[$i] > 0) {
                $currentPrice = $closes[$i];
                break;
            }
        }

        if ($currentPrice <= 0) {
            return null;
        }

        // ATR as percentage of price
        $atrPercent = ($atr / $currentPrice) * 100;

        return [
            'atr' => round($atr, 2),
            'atr_percent' => round($atrPercent, 2),
            'volatility_category' => $this->categorizeVolatility($atrPercent),
            'suggested_stop_loss' => round($currentPrice - (2 * $atr), 2),
            'suggested_position_size' => $this->suggestPositionSize($atrPercent),
        ];
    }

    /**
     * Categorize volatility based on ATR%
     */
    private function categorizeVolatility(float $atrPercent): string
    {
        if ($atrPercent > 10) return 'Extreme';
        if ($atrPercent > 5) return 'High';
        if ($atrPercent > 2) return 'Moderate';
        return 'Low';
    }

    /**
     * Suggest position size based on volatility
     */
    private function suggestPositionSize(float $atrPercent): string
    {
        if ($atrPercent > 10) return 'Very Small (1-2% of portfolio)';
        if ($atrPercent > 5) return 'Small (3-5% of portfolio)';
        if ($atrPercent > 2) return 'Medium (5-8% of portfolio)';
        return 'Normal (8-10% of portfolio)';
    }

    /**
     * Analyze risk metrics (NEW CATEGORY - Phase 1)
     * Includes ATR volatility analysis and liquidity checks
     */
    private function analyzeRiskMetrics(array $data): void
    {
        $category = 'Risk Assessment';
        $points = 0;
        $maxPoints = 15;

        $highs = $data['historical_highs'] ?? [];
        $lows = $data['historical_lows'] ?? [];
        $closes = $data['historical_closes'] ?? [];

        if (count($highs) >= 20 && count($lows) >= 20 && count($closes) >= 20) {
            // ATR Analysis (0-10 points)
            $atr = $this->calculateATR($highs, $lows, $closes);

            if ($atr !== null) {
                $volatility = $atr['volatility_category'];
                $atrPercent = $atr['atr_percent'];

                if ($volatility === 'Low') {
                    $points += 10;
                    $this->addReason('positive', "Low volatility (ATR: {$atrPercent}%) - stable stock, lower risk. {$atr['suggested_position_size']}");
                } elseif ($volatility === 'Moderate') {
                    $points += 7;
                    $this->addReason('neutral', "Moderate volatility (ATR: {$atrPercent}%) - normal price swings. {$atr['suggested_position_size']}");
                } elseif ($volatility === 'High') {
                    $points += 4;
                    $this->addReason('warning', "High volatility (ATR: {$atrPercent}%) - larger price swings. {$atr['suggested_position_size']}");
                } else {
                    $points += 2;
                    $this->addReason('negative', "Extreme volatility (ATR: {$atrPercent}%) - very risky. {$atr['suggested_position_size']}");
                }
            }

            // Liquidity check (0-5 points)
            if ($data['volume'] > 0 && $data['avg_volume'] > 0) {
                $volumeRatio = $data['volume'] / $data['avg_volume'];
                if ($volumeRatio > 0.8) {
                    $points += 5;
                } elseif ($volumeRatio > 0.5) {
                    $points += 3;
                    $this->addReason('warning', "Below average liquidity - monitor for exit opportunities.");
                } else {
                    $points += 1;
                    $this->addReason('warning', "Very low liquidity - may be difficult to exit position.");
                }
            }
        }

        $this->analysis[$category] = [
            'score' => round(($points / $maxPoints) * 100, 2),
            'points' => $points,
            'max_points' => $maxPoints,
        ];

        $this->score += ($points / $maxPoints) * 15; // 15% weight
    }

    /**
     * Analyze foreign/institutional ownership (NEW CATEGORY - Phase 1)
     * Critical for emerging markets like IDX
     */
    private function analyzeForeignFlow(array $data): void
    {
        $category = 'Market Participation';
        $points = 0;
        $maxPoints = 10;

        $institutionalOwnership = $data['held_percent_institutions'] ?? 0;

        if ($institutionalOwnership > 0) {
            // Convert to percentage (data is in decimal format 0-1)
            $foreignPercent = $institutionalOwnership * 100;

            if ($foreignPercent > 50) {
                $points += 10;
                $this->addReason('positive', "Very high institutional ownership ({$foreignPercent}%) - strong international confidence, smart money accumulating.");
            } elseif ($foreignPercent > 30) {
                $points += 7;
                $this->addReason('positive', "High institutional ownership ({$foreignPercent}%) - good foreign interest and liquidity.");
            } elseif ($foreignPercent > 20) {
                $points += 5;
                $this->addReason('neutral', "Moderate institutional ownership ({$foreignPercent}%) - balanced investor base.");
            } elseif ($foreignPercent > 10) {
                $points += 3;
                $this->addReason('neutral', "Low institutional ownership ({$foreignPercent}%) - mostly domestic investors.");
            } else {
                $points += 2;
                $this->addReason('warning', "Very low institutional ownership ({$foreignPercent}%) - limited foreign interest, potential liquidity concerns.");
            }
        } else {
            $points += 2;
            $this->addReason('warning', "No institutional ownership data available - unable to assess foreign interest.");
        }

        // Cap at maxPoints
        $points = min($points, $maxPoints);
        $points = max($points, 0);

        $this->analysis[$category] = [
            'score' => round(($points / $maxPoints) * 100, 2),
            'points' => $points,
            'max_points' => $maxPoints,
        ];

        $this->score += ($points / $maxPoints) * 10; // 10% weight
    }

    // ========================================================================
    // PHASE 2 INDICATORS: Sector Rotation, Liquidity, Sharia, BUMN
    // ========================================================================

    /**
     * Calculate Liquidity Score (IDX-specific)
     * Phase 2 Enhancement - measures ease of buying/selling
     */
    private function calculateLiquidityScore(array $data): array
    {
        $score = 0;
        $maxScore = 100;

        // 1. Average Daily Value (40 points)
        $avgDailyValue = $data['avg_volume'] * $data['current_price'];

        if ($this->currency === 'IDR') {
            // Indonesian stocks - in Rupiah
            if ($avgDailyValue > 100_000_000_000) { // > 100 billion IDR
                $score += 40;
            } elseif ($avgDailyValue > 10_000_000_000) { // > 10 billion IDR
                $score += 30;
            } elseif ($avgDailyValue > 1_000_000_000) { // > 1 billion IDR
                $score += 20;
            } else {
                $score += 10;
            }
        } else {
            // Other markets
            if ($avgDailyValue > 10_000_000) { // > 10M
                $score += 40;
            } elseif ($avgDailyValue > 1_000_000) { // > 1M
                $score += 30;
            } else {
                $score += 20;
            }
        }

        // 2. Volume Consistency (30 points)
        $volumes = $data['historical_volumes'] ?? [];
        if (count($volumes) >= 20) {
            $recentVolumes = array_slice($volumes, -20);
            $avgVolume = array_sum($recentVolumes) / count($recentVolumes);
            $variance = 0;
            foreach ($recentVolumes as $vol) {
                $variance += pow($vol - $avgVolume, 2);
            }
            $stdDev = sqrt($variance / count($recentVolumes));
            $coefficientOfVariation = $avgVolume > 0 ? ($stdDev / $avgVolume) : 1;

            if ($coefficientOfVariation < 0.3) { // Very consistent
                $score += 30;
            } elseif ($coefficientOfVariation < 0.5) { // Moderate consistency
                $score += 20;
            } else {
                $score += 10;
            }
        }

        // 3. Market Cap (30 points) - larger = more liquid
        $marketCap = $data['market_cap'];
        $capInfo = $this->getMarketCapDivisor($this->currency);
        $marketCapValue = $marketCap / $capInfo['divisor'];

        if ($marketCapValue > 50) { // Large cap
            $score += 30;
        } elseif ($marketCapValue > 10) { // Mid-large cap
            $score += 25;
        } elseif ($marketCapValue > 1) { // Mid cap
            $score += 15;
        } else { // Small cap
            $score += 5;
        }

        $category = 'Illiquid';
        if ($score >= 80) $category = 'Very Liquid';
        elseif ($score >= 60) $category = 'Moderately Liquid';
        elseif ($score >= 40) $category = 'Low Liquidity';

        return [
            'score' => $score,
            'max_score' => $maxScore,
            'category' => $category,
            'avg_daily_value' => $avgDailyValue,
            'market_cap_value' => round($marketCapValue, 2),
        ];
    }

    /**
     * Analyze Sharia Compliance
     * Phase 2 Enhancement - Islamic investment criteria
     */
    private function analyzeShariaCompliance(string $symbol, array $data): array
    {
        $cleanSymbol = str_replace('.JK', '', strtoupper($symbol));

        // Check if in DES list
        $isCompliant = IndonesianMarketData::isShariaCompliant($cleanSymbol);

        // Additional criteria check
        $debtRatio = $data['debt_to_equity'] ?? 0;
        $sector = IndonesianMarketData::getSector($cleanSymbol);

        // Non-compliant sectors
        $nonCompliantSectors = ['Banking (Conventional)', 'Alcohol', 'Gambling', 'Pork'];
        $sectorCompliant = !in_array($sector, $nonCompliantSectors);

        // Debt ratio check (should be < 45%)
        $debtCompliant = $debtRatio < 45;

        $notes = [];
        if (!$isCompliant) {
            $notes[] = 'Not in OJK DES (Daftar Efek Syariah) list';
        }
        if (!$sectorCompliant) {
            $notes[] = "Sector ({$sector}) not Sharia-compliant";
        }
        if (!$debtCompliant && $debtRatio > 0) {
            $notes[] = "Debt-to-Equity ratio ({$debtRatio}%) exceeds 45% threshold";
        }

        return [
            'is_compliant' => $isCompliant && $sectorCompliant && $debtCompliant,
            'in_des_list' => $isCompliant,
            'sector' => $sector,
            'sector_compliant' => $sectorCompliant,
            'debt_compliant' => $debtCompliant,
            'notes' => empty($notes) ? ['Stock meets Sharia compliance criteria'] : $notes,
        ];
    }

    /**
     * Analyze BUMN (State-Owned Enterprise) Status
     * Phase 2 Enhancement - government ownership characteristics
     */
    private function analyzeBUMNStatus(string $symbol): array
    {
        $cleanSymbol = str_replace('.JK', '', strtoupper($symbol));

        $isBUMN = IndonesianMarketData::isBUMN($cleanSymbol);
        $bumnInfo = IndonesianMarketData::getBUMNInfo($cleanSymbol);

        $characteristics = [];
        if ($isBUMN && $bumnInfo) {
            $characteristics = [
                'advantages' => [
                    'Government backing reduces bankruptcy risk',
                    'Stable business model (often monopoly/oligopoly)',
                    'Regular dividend payouts (government mandate)',
                    'Better access to government projects',
                ],
                'disadvantages' => [
                    'Bureaucratic management structure',
                    'Political interference in decision-making',
                    'Limited growth due to dividend mandates',
                    'Non-merit based executive appointments possible',
                ],
                'tier' => $bumnInfo['tier'] ?? 'unknown',
                'sector' => $bumnInfo['sector'] ?? 'unknown',
                'gov_ownership' => $bumnInfo['ownership'] ?? 0,
            ];
        }

        return [
            'is_bumn' => $isBUMN,
            'bumn_info' => $bumnInfo,
            'characteristics' => $characteristics,
            'assessment' => $isBUMN ?
                'Government-backed stock: Higher stability, potentially lower growth vs private peers' :
                'Private sector stock: Higher growth potential, higher risk',
        ];
    }

    /**
     * Analyze Sector Performance and Rotation
     * Phase 2 Enhancement - sector momentum analysis
     */
    private function analyzeSectorRotation(string $symbol, array $data): array
    {
        $cleanSymbol = str_replace('.JK', '', strtoupper($symbol));
        $sector = IndonesianMarketData::getSector($cleanSymbol) ?? 'Unknown';

        // Get stock's momentum
        $priceChange = $data['change_percent'];
        $closes = $data['historical_closes'] ?? [];

        $momentum1Week = 0;
        $momentum1Month = 0;

        if (count($closes) >= 5) {
            $momentum1Week = (end($closes) / $closes[count($closes) - 5] - 1) * 100;
        }
        if (count($closes) >= 20) {
            $momentum1Month = (end($closes) / $closes[count($closes) - 20] - 1) * 100;
        }

        // Sector status based on momentum
        $sectorStatus = 'NEUTRAL';
        if ($momentum1Month > 10) {
            $sectorStatus = 'HOT';
        } elseif ($momentum1Month > 5) {
            $sectorStatus = 'WARMING';
        } elseif ($momentum1Month < -10) {
            $sectorStatus = 'COLD';
        } elseif ($momentum1Month < -5) {
            $sectorStatus = 'COOLING';
        }

        $interpretation = match($sectorStatus) {
            'HOT' => "{$sector} sector is outperforming - strong uptrend",
            'WARMING' => "{$sector} sector showing positive momentum",
            'COOLING' => "{$sector} sector showing weakness",
            'COLD' => "{$sector} sector underperforming - downtrend",
            default => "{$sector} sector in neutral zone",
        };

        return [
            'sector' => $sector,
            'sector_status' => $sectorStatus,
            'momentum_1week_percent' => round($momentum1Week, 2),
            'momentum_1month_percent' => round($momentum1Month, 2),
            'interpretation' => $interpretation,
            'recommendation' => $this->getSectorRotationRecommendation($sectorStatus, $data['score'] ?? 0),
        ];
    }

    /**
     * Get sector rotation trading recommendation
     */
    private function getSectorRotationRecommendation(string $sectorStatus, float $stockScore): string
    {
        if ($sectorStatus === 'HOT' && $stockScore > 65) {
            return 'Strong buy - both sector and stock are strong';
        } elseif ($sectorStatus === 'HOT') {
            return 'Consider - sector is hot but stock fundamentals weak';
        } elseif ($sectorStatus === 'COLD' && $stockScore > 75) {
            return 'Wait for sector rotation - good stock in weak sector';
        } elseif ($sectorStatus === 'COLD') {
            return 'Avoid - both sector and stock are weak';
        } else {
            return 'Normal analysis applies - sector neutral';
        }
    }
}
