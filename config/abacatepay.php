<?php

return [
    /*
    |--------------------------------------------------------------------------
    | AbacatePay API Configuration
    |--------------------------------------------------------------------------
    |
    | Configurações para integração com a AbacatePay
    |
    */

    'api_key' => env('ABACATEPAY_API_KEY'),
    
    'base_url' => env('ABACATEPAY_BASE_URL', 'https://api.abacatepay.com/v1'),
    
    'webhook_secret' => env('ABACATEPAY_WEBHOOK_SECRET'),
    
    /*
    |--------------------------------------------------------------------------
    | Configurações de Timeout
    |--------------------------------------------------------------------------
    */
    
    'timeout' => env('ABACATEPAY_TIMEOUT', 30),
    
    /*
    |--------------------------------------------------------------------------
    | Configurações de PIX
    |--------------------------------------------------------------------------
    */
    
    'pix' => [
        'default_expires_in' => 3600, // 1 hora em segundos
        'min_amount' => 100, // R$ 1,00 em centavos
        'max_amount' => 1000000, // R$ 10.000,00 em centavos
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Logs
    |--------------------------------------------------------------------------
    */
    
    'log_requests' => env('ABACATEPAY_LOG_REQUESTS', true),
    'log_webhooks' => env('ABACATEPAY_LOG_WEBHOOKS', true),
]; 