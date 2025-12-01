<?php

namespace App\Data;

class SingaporeStocks
{
    /**
     * Comprehensive list of Singapore stocks on SGX
     * Organized by sector for better scanning
     */
    public static function getAll(): array
    {
        return array_merge(
            self::getBanking(),
            self::getTelecommunication(),
            self::getConsumer(),
            self::getInfrastructure(),
            self::getREITs(),
            self::getManufacturing(),
            self::getHealthcare(),
            self::getTechnology(),
            self::getRetail(),
            self::getTransportation()
        );
    }

    public static function getBanking(): array
    {
        return [
            'D05', // DBS Group Holdings
            'O39', // Oversea-Chinese Banking Corp
            'U11', // United Overseas Bank
        ];
    }

    public static function getTelecommunication(): array
    {
        return [
            'Z74', // Singapore Telecommunications
            'CC3', // StarHub
            'M1', // M1 Limited
        ];
    }

    public static function getConsumer(): array
    {
        return [
            // Food & Beverage
            'F34', // Wilmar International
            'S51', // SATS Ltd
            'Q01', // Thai Beverage
            'N2IU', // Dairy Farm International
            'F17', // Sheng Siong Group
            'H78', // Hongkong Land

            // Consumer Goods
            'Y92', // Thai Beverage
            'U14', // UOL Group
        ];
    }

    public static function getInfrastructure(): array
    {
        return [
            'BN4', // Keppel Corporation
            'U96', // Sembcorp Industries
            'S63', // ST Engineering
            'C52', // ComfortDelGro
            'C09', // City Developments
            'H78', // Hongkong Land Holdings
        ];
    }

    public static function getREITs(): array
    {
        return [
            'J91U', // ESR-LOGOS REIT
            'M44U', // Mapletree Logistics Trust
            'ME8U', // Mapletree Industrial Trust
            'N2IU', // Mapletree Pan Asia Commercial Trust
            'J85', // CapitaLand Integrated Commercial Trust
            'A17U', // CapitaLand Ascendas REIT
            'HMN', // Far East Hospitality Trust
            'C38U', // CapitaLand China Trust
            'T82U', // Suntec REIT
            'K71U', // Keppel REIT
        ];
    }

    public static function getManufacturing(): array
    {
        return [
            'V03', // Venture Corporation
            'AWX', // AEM Holdings
            'U77', // UMS Holdings
            'BN2', // Keppel Infrastructure Trust
            'S68', // Singapore Exchange
        ];
    }

    public static function getHealthcare(): array
    {
        return [
            'BSL', // Raffles Medical Group
            '1D0', // IHH Healthcare
            'VC2', // Sunningdale Tech
        ];
    }

    public static function getTechnology(): array
    {
        return [
            'S68', // Singapore Exchange
            'AWX', // AEM Holdings
            'BN4', // Keppel Corp (Tech division)
            'V03', // Venture Corporation
            'U77', // UMS Holdings
            '5WJ', // Valuetronics Holdings
        ];
    }

    public static function getRetail(): array
    {
        return [
            'F17', // Sheng Siong Group
            'OU8', // Dairy Farm International
            'C07', // Jardine Cycle & Carriage
            'M04', // Mandarin Oriental
        ];
    }

    public static function getTransportation(): array
    {
        return [
            'C6L', // Singapore Airlines
            'S58', // SATS Ltd
            'C52', // ComfortDelGro Corp
            '5TG', // Genting Singapore
        ];
    }

    public static function getFinance(): array
    {
        return [
            'G13', // Genting Singapore
            'S68', // Singapore Exchange
            'U10', // UOB-Kay Hian Holdings
            'L38', // Lendlease Global Commercial REIT
        ];
    }

    /**
     * Get stocks by sector
     */
    public static function getBySector(string $sector): array
    {
        return match(strtolower($sector)) {
            'banking', 'bank' => self::getBanking(),
            'telco', 'telecommunication' => self::getTelecommunication(),
            'consumer' => self::getConsumer(),
            'infrastructure', 'construction' => self::getInfrastructure(),
            'reits', 'reit', 'real estate investment trust' => self::getREITs(),
            'manufacturing' => self::getManufacturing(),
            'healthcare', 'medical' => self::getHealthcare(),
            'technology', 'tech' => self::getTechnology(),
            'retail' => self::getRetail(),
            'transportation', 'logistics', 'airline' => self::getTransportation(),
            'finance', 'financial' => self::getFinance(),
            default => self::getAll()
        };
    }

    /**
     * Get total count
     */
    public static function count(): int
    {
        return count(self::getAll());
    }

    /**
     * Get sectors list
     */
    public static function getSectors(): array
    {
        return [
            'Banking',
            'Telecommunication',
            'Consumer',
            'Infrastructure',
            'REITs',
            'Manufacturing',
            'Healthcare',
            'Technology',
            'Retail',
            'Transportation',
            'Finance'
        ];
    }

    /**
     * Get top 10 preset stocks with BUY/BULLISH signals
     * Curated list based on historical performance and technical analysis
     */
    public static function getTop10BuyPreset(): array
    {
        return [
            // Big 3 Banks (most stable)
            'D05', 'O39', 'U11',

            // Blue Chips
            'Z74', // SingTel
            'C6L', // Singapore Airlines
            'S68', // Singapore Exchange

            // REITs (dividend plays)
            'J91U', // ESR-LOGOS REIT
            'M44U', // Mapletree Logistics Trust

            // Infrastructure
            'BN4', // Keppel Corp
            'U96', // Sembcorp Industries
        ];
    }

    /**
     * Get top 5 preset stocks for scalping/day trading
     * High volatility, good liquidity, frequent price movements
     */
    public static function getTop5ScalpingPreset(): array
    {
        return [
            'C6L', // Singapore Airlines - High volatility
            'BN4', // Keppel Corp - Active trading
            'G13', // Genting Singapore - Casino sector volatility
            'AWX', // AEM Holdings - Tech volatility
            'S68', // Singapore Exchange - Financial sector movements
        ];
    }

    /**
     * Get combined scanner list for quick opportunities
     * Uses preset lists for faster, reliable scanning
     */
    public static function getScannerList(): array
    {
        return array_merge(
            self::getTop10BuyPreset(),
            self::getTop5ScalpingPreset()
        );
    }

    /**
     * Get expanded scanner list with 50 stocks for comprehensive scanning
     * Includes top blue chips, REITs, and high-potential stocks
     */
    public static function getExpandedScannerList(): array
    {
        return [
            // Top 3 Banks (Largest Market Cap)
            'D05', 'O39', 'U11',

            // Top Telco & Transportation
            'Z74', 'CC3', 'C6L', 'C52', 'S58',

            // Top Infrastructure
            'BN4', 'U96', 'S63', 'C09', 'H78',

            // Top 15 REITs (High Dividend, Stable Returns)
            'J91U', 'M44U', 'ME8U', 'N2IU', 'J85',
            'A17U', 'C38U', 'T82U', 'K71U', 'HMN',
            'L38', 'BN2', 'Q5T', 'RW0U', 'AJBU',

            // Top Consumer & F&B
            'F34', 'S51', 'Q01', 'F17', 'U14', 'Y92',

            // Top Manufacturing & Tech
            'V03', 'AWX', 'U77', 'S68', '5WJ',

            // Top Finance & Casino
            'G13', 'U10', '5TG',

            // Top Healthcare & Retail
            'BSL', '1D0', 'VC2', 'OU8', 'C07', 'M04',

            // Additional High-Potential Stocks
            'M1', 'SGX', 'STEL', 'STE', 'KEP',
        ];
    }

    /**
     * Get top 5 big cap stocks (safest, most liquid, for long-term)
     */
    public static function getTop5BigCap(): array
    {
        return [
            'D05', // DBS Group - Largest Singapore bank
            'O39', // OCBC Bank
            'U11', // UOB
            'Z74', // SingTel
            'C6L', // Singapore Airlines
        ];
    }

    /**
     * Get top 50 big cap Singapore stocks
     * Blue chips and most liquid stocks
     */
    public static function getTopBigCap(): array
    {
        return [
            // Top 3 Banks (largest market cap)
            'D05', 'O39', 'U11',

            // Top Telco
            'Z74', 'CC3',

            // Top Transportation
            'C6L', 'S58', 'C52',

            // Top Infrastructure
            'BN4', 'U96', 'S63', 'C09',

            // Top REITs
            'J91U', 'M44U', 'ME8U', 'N2IU', 'J85',
            'A17U', 'C38U', 'T82U', 'K71U',

            // Top Consumer/F&B
            'F34', 'S51', 'Q01', 'F17', 'U14',

            // Top Manufacturing/Tech
            'V03', 'AWX', 'U77', 'S68',

            // Top Finance
            'G13', 'U10',

            // Top Healthcare
            'BSL', '1D0',

            // Top Retail
            'OU8', 'C07', 'M04',

            // Additional liquid stocks
            'H78', 'L38', '5TG', 'Y92', 'VC2',
            'BN2', '5WJ',
        ];
    }
}
