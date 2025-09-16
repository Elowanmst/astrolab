<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Mondial Relay API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for the Mondial Relay API integration.
    | Get your credentials from your Mondial Relay technical contact.
    |
    */

    // Basic API credentials
    'enseigne' => env('MONDIAL_RELAY_ENSEIGNE', 'BDTEST13'),
    'private_key' => env('MONDIAL_RELAY_PRIVATE_KEY', 'TestAPI1key'),
    'brand_id' => env('MONDIAL_RELAY_BRAND_ID', '11'), // Required for permalink tracking links
    'test_mode' => env('MONDIAL_RELAY_TEST_MODE', true),
    'api_url' => env('MONDIAL_RELAY_API_URL', 'https://api.mondialrelay.com/Web_Services.asmx'),

    // Debug configuration
    'debug' => [
        'enabled' => env('MONDIAL_RELAY_DEBUG', false),
        'log_to_file' => env('MONDIAL_RELAY_DEBUG_LOG_FILE', true),
        'display_in_browser' => env('MONDIAL_RELAY_DEBUG_BROWSER', false),
        'log_level' => env('MONDIAL_RELAY_DEBUG_LEVEL', 'info'),
        'mask_sensitive_data' => env('MONDIAL_RELAY_DEBUG_MASK_SENSITIVE', true),
    ],

    // API V2 configuration (REST)
    'api_v2' => [
        'enabled' => env('MONDIAL_RELAY_API_V2_ENABLED', false),
        'url' => env('MONDIAL_RELAY_API_V2_URL', 'https://connect-api.mondialrelay.com/api'),
        'user' => env('MONDIAL_RELAY_API_V2_USER', ''),
        'password' => env('MONDIAL_RELAY_API_V2_PASSWORD', ''),
    ],

    // Security configuration
    'security' => [
        'encrypt_credentials' => env('MONDIAL_RELAY_ENCRYPT_CREDENTIALS', false),
        'validate_ssl' => env('MONDIAL_RELAY_VALIDATE_SSL', true),
        'allowed_ips' => explode(',', env('MONDIAL_RELAY_ALLOWED_IPS', '')),
        'rate_limit' => env('MONDIAL_RELAY_RATE_LIMIT', 100), // requests per minute
    ],

    // Cache configuration
    'cache' => [
        'enabled' => env('MONDIAL_RELAY_CACHE_ENABLED', true),
        'ttl' => env('MONDIAL_RELAY_CACHE_TTL', 3600), // 1 hour
        'prefix' => env('MONDIAL_RELAY_CACHE_PREFIX', 'mondial_relay_'),
        'store' => env('MONDIAL_RELAY_CACHE_STORE', 'default'),
    ],

    // Retry configuration
    'retry' => [
        'enabled' => env('MONDIAL_RELAY_RETRY_ENABLED', true),
        'max_attempts' => env('MONDIAL_RELAY_RETRY_MAX_ATTEMPTS', 3),
        'delay' => env('MONDIAL_RELAY_RETRY_DELAY', 1000), // milliseconds
        'backoff_multiplier' => env('MONDIAL_RELAY_RETRY_BACKOFF', 2),
        'retry_on_codes' => [99], // System errors
    ],

    // Timeout configuration
    'timeout' => [
        'connection' => env('MONDIAL_RELAY_TIMEOUT_CONNECTION', 10),
        'request' => env('MONDIAL_RELAY_TIMEOUT_REQUEST', 30),
        'soap' => env('MONDIAL_RELAY_TIMEOUT_SOAP', 60),
        'rest' => env('MONDIAL_RELAY_TIMEOUT_REST', 30),
    ],

    // Default values for expeditions
    'defaults' => [
        'delivery_mode' => env('MONDIAL_RELAY_DEFAULT_DELIVERY_MODE', '24R'),
        'collection_mode' => env('MONDIAL_RELAY_DEFAULT_COLLECTION_MODE', 'CCC'),
        'language' => env('MONDIAL_RELAY_DEFAULT_LANGUAGE', 'FR'),
        'currency' => env('MONDIAL_RELAY_DEFAULT_CURRENCY', 'EUR'),
        'insurance_level' => env('MONDIAL_RELAY_DEFAULT_INSURANCE', '0'),
    ],
];
