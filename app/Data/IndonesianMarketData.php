<?php

namespace App\Data;

/**
 * Indonesian Market-Specific Data
 * Phase 2 Enhancement: Sharia Compliance and BUMN Status
 */
class IndonesianMarketData
{
    /**
     * Sharia-compliant stocks (DES - Daftar Efek Syariah)
     * Updated periodically by OJK (Otoritas Jasa Keuangan)
     *
     * Note: This is a sample list. In production, should be updated quarterly
     * from official OJK DES list
     */
    public static function getShariaCompliantStocks(): array
    {
        return [
            // Banking (Sharia banks only)
            'BRIS', 'BTPS', 'BSIM', 'MAYA',

            // Consumer Goods
            'UNVR', 'ICBP', 'INDF', 'MYOR', 'ULTJ', 'ROTI', 'MLBI',
            'KLBF', 'SIDO', 'TSPC', 'GGRM', 'HMSP', 'DLTA', 'GOOD',

            // Telecommunications
            'TLKM', 'EXCL', 'ISAT', 'FREN',

            // Infrastructure & Construction
            'JSMR', 'WIKA', 'WSKT', 'PTPP', 'WSBP', 'ADHI', 'TOTL',

            // Energy & Mining
            'PGAS', 'ADRO', 'PTBA', 'ANTM', 'INCO', 'MEDC', 'ELSA',

            // Manufacturing & Automotive
            'ASII', 'UNTR', 'AUTO', 'IMAS', 'SMSM', 'INDS', 'SMGR',
            'INTP', 'SMBR', 'WTON',

            // Property & Real Estate
            'BSDE', 'CTRA', 'PWON', 'SMRA', 'ASRI', 'BKSL', 'APLN',
            'DILD', 'LPKR', 'MKPI', 'BEST',

            // Transportation
            'BIRD', 'BLTA', 'GIAA', 'CMPP',

            // Technology
            'GOTO', 'BUKA', 'EMTK',

            // Agriculture
            'AALI', 'LSIP', 'SIMP', 'TAPG', 'SGRO', 'TBLA',

            // Healthcare
            'HEAL', 'MIKA', 'SILO', 'SAME', 'PRDA',

            // Retail
            'ACES', 'MAPI', 'ERAA', 'RALS', 'MKNT',
        ];
    }

    /**
     * BUMN (Badan Usaha Milik Negara) - State-Owned Enterprises
     * Government ownership > 50%
     */
    public static function getBUMNStocks(): array
    {
        return [
            // Strategic BUMNs (Tier 1)
            'BBRI' => ['tier' => 'strategic', 'sector' => 'Banking', 'ownership' => 56.7],
            'BMRI' => ['tier' => 'strategic', 'sector' => 'Banking', 'ownership' => 60.0],
            'BBNI' => ['tier' => 'strategic', 'sector' => 'Banking', 'ownership' => 60.0],
            'BBTN' => ['tier' => 'strategic', 'sector' => 'Banking', 'ownership' => 100.0],
            'TLKM' => ['tier' => 'strategic', 'sector' => 'Telecommunications', 'ownership' => 52.1],
            'PGAS' => ['tier' => 'strategic', 'sector' => 'Energy', 'ownership' => 56.9],

            // Sectoral BUMNs (Tier 2)
            'ASII' => ['tier' => 'sectoral', 'sector' => 'Manufacturing', 'ownership' => 50.1],
            'ANTM' => ['tier' => 'sectoral', 'sector' => 'Mining', 'ownership' => 65.0],
            'PTBA' => ['tier' => 'sectoral', 'sector' => 'Mining', 'ownership' => 65.0],
            'INCO' => ['tier' => 'sectoral', 'sector' => 'Mining', 'ownership' => 65.0],
            'TINS' => ['tier' => 'sectoral', 'sector' => 'Mining', 'ownership' => 65.0],

            // Infrastructure BUMNs
            'JSMR' => ['tier' => 'sectoral', 'sector' => 'Infrastructure', 'ownership' => 70.0],
            'WIKA' => ['tier' => 'sectoral', 'sector' => 'Construction', 'ownership' => 65.0],
            'WSKT' => ['tier' => 'sectoral', 'sector' => 'Construction', 'ownership' => 66.3],
            'ADHI' => ['tier' => 'sectoral', 'sector' => 'Construction', 'ownership' => 51.0],
            'PTPP' => ['tier' => 'sectoral', 'sector' => 'Construction', 'ownership' => 51.0],

            // Manufacturing BUMNs
            'SMGR' => ['tier' => 'sectoral', 'sector' => 'Cement', 'ownership' => 51.0],
            'INTP' => ['tier' => 'sectoral', 'sector' => 'Cement', 'ownership' => 51.0],
            'SMBR' => ['tier' => 'sectoral', 'sector' => 'Cement', 'ownership' => 55.0],

            // Pharmaceutical
            'KAEF' => ['tier' => 'sectoral', 'sector' => 'Pharmaceutical', 'ownership' => 77.8],
            'INAF' => ['tier' => 'sectoral', 'sector' => 'Pharmaceutical', 'ownership' => 81.6],

            // Transportation
            'GIAA' => ['tier' => 'sectoral', 'sector' => 'Aviation', 'ownership' => 60.5],
        ];
    }

    /**
     * Sector mappings for IDX stocks
     */
    public static function getSectorMap(): array
    {
        return [
            // Banking
            'BBCA' => 'Banking', 'BBRI' => 'Banking', 'BMRI' => 'Banking',
            'BBNI' => 'Banking', 'BBTN' => 'Banking', 'BRIS' => 'Banking',
            'MEGA' => 'Banking', 'BDMN' => 'Banking', 'NISP' => 'Banking',

            // Telecommunications
            'TLKM' => 'Telecommunications', 'EXCL' => 'Telecommunications',
            'ISAT' => 'Telecommunications', 'FREN' => 'Telecommunications',

            // Consumer
            'UNVR' => 'Consumer Goods', 'ICBP' => 'Food & Beverage',
            'INDF' => 'Food & Beverage', 'MYOR' => 'Food & Beverage',
            'GGRM' => 'Tobacco', 'HMSP' => 'Tobacco',

            // Infrastructure
            'JSMR' => 'Infrastructure', 'WIKA' => 'Construction',
            'WSKT' => 'Construction', 'PTPP' => 'Construction',
            'ADHI' => 'Construction',

            // Mining & Energy
            'ADRO' => 'Mining', 'PTBA' => 'Mining', 'ANTM' => 'Mining',
            'INCO' => 'Mining', 'PGAS' => 'Energy',

            // Manufacturing
            'ASII' => 'Automotive', 'UNTR' => 'Automotive',
            'AUTO' => 'Automotive', 'SMGR' => 'Cement',
            'INTP' => 'Cement', 'SMBR' => 'Cement',

            // Technology
            'GOTO' => 'Technology', 'BUKA' => 'Technology',
            'EMTK' => 'Media & Technology',

            // Property
            'BSDE' => 'Property', 'CTRA' => 'Property',
            'PWON' => 'Property', 'SMRA' => 'Property',
        ];
    }

    /**
     * Check if stock is Sharia-compliant
     */
    public static function isShariaCompliant(string $symbol): bool
    {
        // Remove .JK suffix if present
        $symbol = str_replace('.JK', '', strtoupper($symbol));
        return in_array($symbol, self::getShariaCompliantStocks());
    }

    /**
     * Check if stock is BUMN
     */
    public static function isBUMN(string $symbol): bool
    {
        $symbol = str_replace('.JK', '', strtoupper($symbol));
        return array_key_exists($symbol, self::getBUMNStocks());
    }

    /**
     * Get BUMN info for a stock
     */
    public static function getBUMNInfo(string $symbol): ?array
    {
        $symbol = str_replace('.JK', '', strtoupper($symbol));
        return self::getBUMNStocks()[$symbol] ?? null;
    }

    /**
     * Get sector for a stock
     */
    public static function getSector(string $symbol): ?string
    {
        $symbol = str_replace('.JK', '', strtoupper($symbol));
        return self::getSectorMap()[$symbol] ?? null;
    }

    /**
     * Get all stocks in a sector
     */
    public static function getStocksInSector(string $sector): array
    {
        return array_keys(array_filter(
            self::getSectorMap(),
            fn($s) => $s === $sector
        ));
    }
}
