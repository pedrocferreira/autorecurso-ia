<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Google OAuth Configuration
    |--------------------------------------------------------------------------
    */
    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URL', env('APP_URL') . '/auth/google/callback'),
    ],

    /*
    |--------------------------------------------------------------------------
    | AI Services Configuration
    |--------------------------------------------------------------------------
    */
    
    // Google Gemini Pro - Inteligência Artificial da Google
    'gemini' => [
        'api_key' => env('GEMINI_API_KEY'),
        'enabled' => env('GEMINI_ENABLED', true),
        'model' => env('GEMINI_MODEL', 'gemini-1.5-pro-latest'),
        'url' => env('GEMINI_URL', 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent'),
        'max_tokens' => env('GEMINI_MAX_TOKENS', 2500),
        'temperature' => env('GEMINI_TEMPERATURE', 0.4),
    ],

    // Hugging Face - Para RoBERTaLexPT e outros modelos
    'huggingface' => [
        'api_key' => env('HUGGINGFACE_API_KEY'),
        'enabled' => env('HUGGINGFACE_ENABLED', false),
        'roberta_model' => env('ROBERTA_MODEL', 'eduagarcia/RoBERTaLexPT-base'),
        'saul_model' => env('SAUL_MODEL', 'Equall/Saul-7B-Instruct-v1'),
    ],

    // OpenAI GPT-4 - Configuração já existente mas centralizada aqui
    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
        'enabled' => env('OPENAI_ENABLED', true),
        'model' => env('OPENAI_MODEL', 'gpt-4-turbo'),
        'max_tokens' => env('OPENAI_MAX_TOKENS', 2500),
        'temperature' => env('OPENAI_TEMPERATURE', 0.4),
    ],

    // Claude - Anthropic (se necessário no futuro)
    'claude' => [
        'api_key' => env('CLAUDE_API_KEY'),
        'enabled' => env('CLAUDE_ENABLED', false),
        'model' => env('CLAUDE_MODEL', 'claude-3-5-sonnet-20241022'),
        'max_tokens' => env('CLAUDE_MAX_TOKENS', 2500),
    ],

    /*
    |--------------------------------------------------------------------------
    | Payment Services
    |--------------------------------------------------------------------------
    */
    
    'abacatepay' => [
        'api_key' => env('ABACATEPAY_API_KEY'),
        'api_url' => env('ABACATEPAY_API_URL', 'https://api.abacatepay.com/v1'),
        'webhook_url' => env('ABACATEPAY_WEBHOOK_URL'),
        'dev_mode' => env('ABACATEPAY_DEV_MODE', false),
    ],

    'stripe' => [
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
    ],

    /*
    |--------------------------------------------------------------------------
    | External APIs
    |--------------------------------------------------------------------------
    */
    
    'brasil_api' => [
        'token' => env('VEHICLE_API_TOKEN'),
        'base_url' => env('BRASIL_API_URL', 'https://api.brasil.io/v1'),
    ],

    'apibrasil' => [
        'token' => env('APIBRASIL_BEARER_TOKEN'),
        'base_url' => env('APIBRASIL_BASE_URL', 'https://api.apibrasil.io/v1'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Email Services
    |--------------------------------------------------------------------------
    */
    
    'brevo' => [
        'api_key' => env('BREVO_API_KEY'),
        'enabled' => (bool) env('BREVO_ENABLED', false),
    ],

]; 