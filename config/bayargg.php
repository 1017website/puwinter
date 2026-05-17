<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Bayar.gg API Configuration
    |--------------------------------------------------------------------------
    | Isi API Key dari dashboard bayar.gg
    | https://www.bayar.gg/api-docs
    */

    'api_key'        => env('BAYARGG_API_KEY', ''),
    'base_url'       => env('BAYARGG_BASE_URL', 'https://www.bayar.gg/api'),

    /*
    |--------------------------------------------------------------------------
    | Payment Method Default
    |--------------------------------------------------------------------------
    | Pilihan: qris | qris_user | gopay_qris | ovo
    | - qris        : QRIS Admin, max Rp 500.000
    | - gopay_qris  : GoPay Merchant, tanpa limit (rekomendasi)
    | - qris_user   : BRI Merchant, tanpa limit
    | - ovo         : OVO Direct
    */

    'payment_method' => env('BAYARGG_PAYMENT_METHOD', 'gopay_qris'),

    /*
    |--------------------------------------------------------------------------
    | Callback & Redirect URL
    |--------------------------------------------------------------------------
    */

    'callback_url'   => env('BAYARGG_CALLBACK_URL', ''),  // diisi otomatis di service
    'redirect_url'   => env('BAYARGG_REDIRECT_URL', ''),  // diisi otomatis di service
];
