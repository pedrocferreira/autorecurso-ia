<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configurações das APIs de Multas de Trânsito
    |--------------------------------------------------------------------------
    |
    | Este arquivo contém as configurações para integração com APIs oficiais
    | de busca de multas de trânsito (DETRAN, DENATRAN, SPP)
    |
    */

    'enabled' => env('TRAFFIC_TICKETS_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Configurações do DETRAN (Estadual)
    |--------------------------------------------------------------------------
    */
    'detran' => [
        'enabled' => env('DETRAN_ENABLED', true),
        'base_url' => env('DETRAN_BASE_URL', 'https://api.detran.gov.br'),
        'api_key' => env('DETRAN_API_KEY'),
        'timeout' => env('DETRAN_TIMEOUT', 30),
        'retry_attempts' => env('DETRAN_RETRY_ATTEMPTS', 3),
    ],

    /*
    |--------------------------------------------------------------------------
    | Configurações do DENATRAN (Federal)
    |--------------------------------------------------------------------------
    */
    'denatran' => [
        'enabled' => env('DENATRAN_ENABLED', true),
        'base_url' => env('DENATRAN_BASE_URL', 'https://api.denatran.gov.br'),
        'api_key' => env('DENATRAN_API_KEY'),
        'timeout' => env('DENATRAN_TIMEOUT', 30),
        'retry_attempts' => env('DENATRAN_RETRY_ATTEMPTS', 3),
    ],

    /*
    |--------------------------------------------------------------------------
    | Configurações do SPP (Sistema de Pontuação)
    |--------------------------------------------------------------------------
    */
    'spp' => [
        'enabled' => env('SPP_ENABLED', true),
        'base_url' => env('SPP_BASE_URL', 'https://api.spp.gov.br'),
        'api_key' => env('SPP_API_KEY'),
        'timeout' => env('SPP_TIMEOUT', 30),
        'retry_attempts' => env('SPP_RETRY_ATTEMPTS', 3),
    ],

    /*
    |--------------------------------------------------------------------------
    | Configurações de Cache
    |--------------------------------------------------------------------------
    */
    'cache' => [
        'enabled' => env('TRAFFIC_TICKETS_CACHE_ENABLED', true),
        'ttl' => env('TRAFFIC_TICKETS_CACHE_TTL', 3600), // 1 hora
        'prefix' => env('TRAFFIC_TICKETS_CACHE_PREFIX', 'traffic_tickets'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Configurações de Simulação (Desenvolvimento)
    |--------------------------------------------------------------------------
    */
    'simulation' => [
        'enabled' => env('TRAFFIC_TICKETS_SIMULATION_ENABLED', true),
        'generate_realistic_data' => env('TRAFFIC_TICKETS_REALISTIC_DATA', true),
        'max_tickets_per_search' => env('TRAFFIC_TICKETS_MAX_TICKETS', 10),
    ],

    /*
    |--------------------------------------------------------------------------
    | Configurações de Log
    |--------------------------------------------------------------------------
    */
    'logging' => [
        'enabled' => env('TRAFFIC_TICKETS_LOGGING_ENABLED', true),
        'level' => env('TRAFFIC_TICKETS_LOG_LEVEL', 'info'),
        'mask_sensitive_data' => env('TRAFFIC_TICKETS_MASK_DATA', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Configurações de Rate Limiting
    |--------------------------------------------------------------------------
    */
    'rate_limiting' => [
        'enabled' => env('TRAFFIC_TICKETS_RATE_LIMITING_ENABLED', true),
        'max_requests_per_minute' => env('TRAFFIC_TICKETS_MAX_REQUESTS_PER_MINUTE', 60),
        'max_requests_per_hour' => env('TRAFFIC_TICKETS_MAX_REQUESTS_PER_HOUR', 1000),
    ],

    /*
    |--------------------------------------------------------------------------
    | Configurações de Notificações
    |--------------------------------------------------------------------------
    */
    'notifications' => [
        'enabled' => env('TRAFFIC_TICKETS_NOTIFICATIONS_ENABLED', true),
        'email_on_new_tickets' => env('TRAFFIC_TICKETS_EMAIL_NOTIFICATIONS', false),
        'push_notifications' => env('TRAFFIC_TICKETS_PUSH_NOTIFICATIONS', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Configurações de Sincronização
    |--------------------------------------------------------------------------
    */
    'sync' => [
        'enabled' => env('TRAFFIC_TICKETS_SYNC_ENABLED', true),
        'auto_sync_interval' => env('TRAFFIC_TICKETS_AUTO_SYNC_INTERVAL', 86400), // 24 horas
        'sync_on_login' => env('TRAFFIC_TICKETS_SYNC_ON_LOGIN', true),
        'sync_on_dashboard' => env('TRAFFIC_TICKETS_SYNC_ON_DASHBOARD', false),
    ],
];
