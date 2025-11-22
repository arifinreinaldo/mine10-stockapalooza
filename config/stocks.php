<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Indonesian Stock Symbols
    |--------------------------------------------------------------------------
    |
    | Popular stocks on the Indonesia Stock Exchange (IDX)
    | All symbols should have .JK suffix for Yahoo Finance
    |
    */

    'idx_blue_chips' => [
        'BBCA.JK' => 'Bank Central Asia Tbk',
        'BBRI.JK' => 'Bank Rakyat Indonesia Tbk',
        'BMRI.JK' => 'Bank Mandiri Tbk',
        'BBNI.JK' => 'Bank Negara Indonesia Tbk',
        'TLKM.JK' => 'Telkom Indonesia Tbk',
        'ASII.JK' => 'Astra International Tbk',
        'UNVR.JK' => 'Unilever Indonesia Tbk',
        'ICBP.JK' => 'Indofood CBP Sukses Makmur Tbk',
        'INDF.JK' => 'Indofood Sukses Makmur Tbk',
        'KLBF.JK' => 'Kalbe Farma Tbk',
    ],

    'idx_tech' => [
        'GOTO.JK' => 'GoTo Gojek Tokopedia Tbk',
        'BUKA.JK' => 'Bukalapak.com Tbk',
        'EXCL.JK' => 'XL Axiata Tbk',
        'ISAT.JK' => 'Indosat Tbk',
    ],

    'idx_consumer' => [
        'UNVR.JK' => 'Unilever Indonesia Tbk',
        'ICBP.JK' => 'Indofood CBP Tbk',
        'INDF.JK' => 'Indofood Sukses Makmur Tbk',
        'MYOR.JK' => 'Mayora Indah Tbk',
        'GGRM.JK' => 'Gudang Garam Tbk',
    ],

    'idx_mining' => [
        'ANTM.JK' => 'Aneka Tambang Tbk',
        'INCO.JK' => 'Vale Indonesia Tbk',
        'PTBA.JK' => 'Bukit Asam Tbk',
        'ADRO.JK' => 'Adaro Energy Tbk',
    ],

    'idx_infrastructure' => [
        'JSMR.JK' => 'Jasa Marga Tbk',
        'WIKA.JK' => 'Wijaya Karya Tbk',
        'WSKT.JK' => 'Waskita Karya Tbk',
        'PTPP.JK' => 'PP (Persero) Tbk',
    ],

    /*
    |--------------------------------------------------------------------------
    | Singapore Stock Symbols
    |--------------------------------------------------------------------------
    |
    | Popular stocks on the Singapore Exchange (SGX)
    | All symbols should have .SI suffix for Yahoo Finance
    |
    */

    'sgx_blue_chips' => [
        'D05.SI' => 'DBS Group Holdings Ltd',
        'O39.SI' => 'Oversea-Chinese Banking Corp',
        'U11.SI' => 'United Overseas Bank Ltd',
        'Z74.SI' => 'Singapore Telecommunications Ltd',
        'C6L.SI' => 'Singapore Airlines Ltd',
        'BN4.SI' => 'Keppel Corp Ltd',
        'U96.SI' => 'Sembcorp Industries Ltd',
        'C52.SI' => 'ComfortDelGro Corp Ltd',
        'G13.SI' => 'Genting Singapore Ltd',
        'S68.SI' => 'Singapore Exchange Ltd',
    ],

    'sgx_reits' => [
        'J91U.SI' => 'ESR-LOGOS REIT',
        'M44U.SI' => 'Mapletree Logistics Trust',
        'ME8U.SI' => 'Mapletree Industrial Trust',
        'N2IU.SI' => 'Mapletree Ind Trust',
        'J85.SI' => 'CapitaLand Integrated Commercial Trust',
    ],

    'sgx_tech' => [
        'S51.SI' => 'SATS Ltd',
        'AWX.SI' => 'AEM Holdings Ltd',
        'U14.SI' => 'UOL Group Ltd',
        'V03.SI' => 'Venture Corp Ltd',
    ],

    'sgx_consumer' => [
        'F34.SI' => 'Wilmar International Ltd',
        'S58.SI' => 'SATS Ltd',
        'U10.SI' => 'UOB-Kay Hian Holdings Ltd',
        'BS6.SI' => 'YTL Corp',
    ],

    /*
    |--------------------------------------------------------------------------
    | Analysis Weights
    |--------------------------------------------------------------------------
    |
    | Weight distribution for different analysis categories (must sum to 100)
    |
    */

    'analysis_weights' => [
        'fundamental' => 20,  // P/E, P/B, EPS
        'technical' => 25,     // RSI, Moving Averages, Momentum
        'valuation' => 15,     // Target price, Analyst recommendations
        'financial_health' => 20, // ROE, Profit Margin, Debt
        'momentum' => 10,      // Volume, Market Cap
        'dividend' => 10,      // Dividend Yield
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Settings
    |--------------------------------------------------------------------------
    */

    'cache_ttl' => env('STOCK_CACHE_TTL', 300), // 5 minutes default

];
