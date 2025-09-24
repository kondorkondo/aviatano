<?php

return [
    /*
    |--------------------------------------------------------------------------
    | ZenoPay API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for ZenoPay mobile money payment integration
    | for Tanzanian mobile money services.
    |
    */

    'api_key' => env('ZENOPAY_API_KEY', 'CKCkUkNFggR1rfPKvzzDDOjv_JNhZTVVZBaTqDPp7pbSc2Nvd7IB7WeHPV43lOtqxc9-nUyVwuWgI6y0767rmA'),
    
    'base_url' => env('ZENOPAY_BASE_URL', 'https://zenoapi.com/api/payments'),
    
    'webhook_url' => env('ZENOPAY_WEBHOOK_URL', 'http://127.0.0.1:8001/api/zenopay/webhook'),
    
    'timeout' => env('ZENOPAY_TIMEOUT', 30),
    
    'verify_ssl' => env('ZENOPAY_VERIFY_SSL', true),
    
    /*
    |--------------------------------------------------------------------------
    | Payment Gateway Settings
    |--------------------------------------------------------------------------
    */
    
    'gateway_id' => 10, // Unique ID for ZenoPay in our system
    
    'gateway_name' => 'Mobile Money Tanzania',
    
    'gateway_alias' => 'mobile_money_tanzania',
    
    'min_amount' => env('ZENOPAY_MIN_AMOUNT', 1000), // Minimum amount in TZS
    
    'max_amount' => env('ZENOPAY_MAX_AMOUNT', 100000), // Maximum amount in TZS
    
    'currency' => 'TZS',
    
    'currency_symbol' => 'TSh',
];
