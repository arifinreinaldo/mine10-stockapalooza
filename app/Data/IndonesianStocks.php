<?php

namespace App\Data;

class IndonesianStocks
{
    /**
     * Comprehensive list of Indonesian stocks on IDX
     * Organized by sector for better scanning
     */
    public static function getAll(): array
    {
        return array_merge(
            self::getBanking(),
            self::getTelecommunication(),
            self::getConsumer(),
            self::getInfrastructure(),
            self::getEnergy(),
            self::getManufacturing(),
            self::getProperty(),
            self::getHealthcare(),
            self::getTechnology(),
            self::getRetail(),
            self::getTransportation(),
            self::getMining()
        );
    }

    public static function getBanking(): array
    {
        return [
            'BBCA', 'BBRI', 'BMRI', 'BBNI', 'BBTN', 'BRIS', 'MEGA', 'BDMN',
            'NISP', 'PNBN', 'BNLI', 'BNGA', 'MAYA', 'BJTM', 'NOBU', 'ARTO',
            'AGRO', 'BSIM', 'BTPN', 'BINA', 'BMAS', 'SDRA', 'BABP', 'BANK'
        ];
    }

    public static function getTelecommunication(): array
    {
        return [
            'TLKM', 'EXCL', 'ISAT', 'FREN', 'BTEL', 'INVS', 'KBLV'
        ];
    }

    public static function getConsumer(): array
    {
        return [
            // Food & Beverage
            'INDF', 'ICBP', 'MYOR', 'ULTJ', 'ROTI', 'MLBI', 'SKBM', 'PSDN',
            'CAMP', 'SKLT', 'ALTO', 'DLTA', 'GOOD', 'STTP', 'AISA', 'CPRO',
            // Consumer Goods
            'UNVR', 'KLBF', 'KAEF', 'MERK', 'SIDO', 'PYFA', 'TSPC', 'KINO',
            'WOOD', 'ADES', 'CEKA', 'DVLA', 'MBTO', 'WIIM',
            // Tobacco
            'GGRM', 'HMSP', 'RMBA', 'WIIM'
        ];
    }

    public static function getInfrastructure(): array
    {
        return [
            'JSMR', 'WIKA', 'WSKT', 'PTPP', 'WSBP', 'ADHI', 'ACST', 'TOTL',
            'NRCA', 'CSIS', 'SSIA', 'DGIK', 'PTDU'
        ];
    }

    public static function getEnergy(): array
    {
        return [
            // Oil & Gas
            'PGAS', 'MEDC', 'ELSA', 'ESSA', 'ENRG', 'RUIS', 'APEX',
            // Power
            'POWR', 'FIRE', 'SUPR', 'RAJA', 'TOBA'
        ];
    }

    public static function getManufacturing(): array
    {
        return [
            // Automotive
            'ASII', 'UNTR', 'AUTO', 'IMAS', 'SMSM', 'INDS', 'NIPS', 'GDYR',
            'GJTL', 'BOLT', 'PRAS',
            // Cement
            'SMGR', 'INTP', 'SMBR', 'WSBP', 'WTON',
            // Steel & Metal
            'BTON', 'LION', 'INAI', 'JPFA', 'TBMS', 'BAJA', 'NIKL', 'MOLI',
            // Paper & Packaging
            'TKIM', 'FASW', 'SPMA', 'AKPI', 'SAIP', 'TALF'
        ];
    }

    public static function getProperty(): array
    {
        return [
            'BSDE', 'CTRA', 'PWON', 'SMRA', 'ASRI', 'BKSL', 'APLN', 'DILD',
            'LPKR', 'MKPI', 'BEST', 'COWL', 'KIJA', 'MDLN', 'PPRO', 'EMDE',
            'NIRO', 'GPRA', 'GWSA', 'OMRE', 'RDTX', 'SMDM', 'TARA'
        ];
    }

    public static function getHealthcare(): array
    {
        return [
            // Pharmaceutical
            'KLBF', 'KAEF', 'SIDO', 'MERK', 'PYFA', 'PEHA', 'SOHO', 'TSPC',
            // Healthcare Services
            'SILO', 'MIKA', 'HEAL', 'SAME', 'PRDA'
        ];
    }

    public static function getTechnology(): array
    {
        return [
            'GOTO', 'BUKA', 'BELI', 'CASH', 'MTDL', 'DNET', 'EXCL', 'LINK'
        ];
    }

    public static function getRetail(): array
    {
        return [
            'ACES', 'MAPI', 'RALS', 'LPPF', 'ERAA', 'MPPA', 'RANC', 'HERO',
            'CSAP', 'GLOB', 'KPAS', 'SONA', 'SKYB'
        ];
    }

    public static function getTransportation(): array
    {
        return [
            // Airlines
            'BIRD', 'CMPP',
            // Logistics
            'GIAA', 'WEHA', 'TPMA', 'NELY', 'ASSA',
            // Shipping
            'IATA', 'SHIP', 'TBLA', 'SMDR', 'KARW'
        ];
    }

    public static function getMining(): array
    {
        return [
            // Coal
            'ADRO', 'PTBA', 'ITMG', 'BSSR', 'HRUM', 'KKGI', 'MBAP', 'MYOH',
            'BYAN', 'DEWA', 'GTBO', 'BUMI', 'ARII', 'DOID', 'SMMT',
            // Metal & Minerals
            'ANTM', 'INCO', 'TINS', 'MDKA', 'CITA', 'PSAB', 'GEMS', 'MITI',
            'BRMS', 'CNKO', 'TMPI', 'BASS', 'ZINC'
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
            'energy', 'oil', 'gas' => self::getEnergy(),
            'manufacturing', 'automotive', 'cement' => self::getManufacturing(),
            'property', 'real estate' => self::getProperty(),
            'healthcare', 'pharma' => self::getHealthcare(),
            'technology', 'tech' => self::getTechnology(),
            'retail' => self::getRetail(),
            'transportation', 'logistics' => self::getTransportation(),
            'mining', 'coal' => self::getMining(),
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
            'Energy',
            'Manufacturing',
            'Property',
            'Healthcare',
            'Technology',
            'Retail',
            'Transportation',
            'Mining'
        ];
    }
}
