<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Configuration Sécurité Production
    |--------------------------------------------------------------------------
    |
    | Configuration des fonctionnalités de sécurité pour la production
    |
    */

    'headers' => [
        'enabled' => env('SECURITY_HEADERS_ENABLED', true),
        
        // Protection XSS
        'x_content_type_options' => 'nosniff',
        'x_frame_options' => 'DENY',
        'x_xss_protection' => '1; mode=block',
        
        // Politique de sécurité du contenu
        'content_security_policy' => [
            'default-src' => "'self'",
            'script-src' => "'self' 'unsafe-inline' 'unsafe-eval' js.stripe.com",
            'style-src' => "'self' 'unsafe-inline' fonts.googleapis.com",
            'font-src' => "'self' fonts.gstatic.com",
            'img-src' => "'self' data: https:",
            'connect-src' => "'self' api.stripe.com",
            'frame-src' => "'self' js.stripe.com hooks.stripe.com",
        ],
        
        // Transport sécurisé (HTTPS)
        'strict_transport_security' => [
            'max_age' => 31536000,
            'include_subdomains' => true,
            'preload' => true,
        ],
        
        // Politique de référent
        'referrer_policy' => 'strict-origin-when-cross-origin',
        
        // Permissions
        'permissions_policy' => [
            'camera' => '()',
            'microphone' => '()',
            'geolocation' => '()',
            'payment' => '(self)',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    */

    'rate_limiting' => [
        'enabled' => env('RATE_LIMITING_ENABLED', true),
        
        'global' => [
            'requests' => 1000,
            'per_minute' => 1,
        ],
        
        'api' => [
            'requests' => 60,
            'per_minute' => 1,
        ],
        
        'auth' => [
            'login' => [
                'requests' => 5,
                'per_minute' => 1,
                'block_duration' => 15, // minutes
            ],
            'register' => [
                'requests' => 3,
                'per_minute' => 1,
            ],
        ],
        
        'checkout' => [
            'requests' => 10,
            'per_minute' => 1,
        ],
        
        'payment' => [
            'requests' => 3,
            'per_minute' => 1,
            'block_duration' => 30, // minutes
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Protection CSRF
    |--------------------------------------------------------------------------
    */

    'csrf' => [
        'enabled' => env('CSRF_PROTECTION_ENABLED', true),
        'except' => [
            'stripe/webhook',
            'paypal/webhook',
            'lyra/webhook',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Protection XSS
    |--------------------------------------------------------------------------
    */

    'xss' => [
        'enabled' => env('XSS_PROTECTION_ENABLED', true),
        'clean_input' => true,
        'allowed_tags' => '<p><br><strong><em><u><a>',
    ],

    /*
    |--------------------------------------------------------------------------
    | Validation de fichiers
    |--------------------------------------------------------------------------
    */

    'file_uploads' => [
        'max_size' => 5120, // KB
        'allowed_types' => [
            'images' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
            'documents' => ['pdf', 'doc', 'docx'],
        ],
        'scan_uploads' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Logs de sécurité
    |--------------------------------------------------------------------------
    */

    'logging' => [
        'channel' => env('LOG_SECURITY_CHANNEL', 'security'),
        'log_failed_logins' => true,
        'log_successful_logins' => false,
        'log_payment_attempts' => true,
        'log_suspicious_activity' => true,
        'retention_days' => 90,
    ],

    /*
    |--------------------------------------------------------------------------
    | Chiffrement avancé
    |--------------------------------------------------------------------------
    */

    'encryption' => [
        'sensitive_fields' => [
            'payment_data',
            'user_tokens',
            'api_keys',
        ],
        'algorithm' => 'AES-256-GCM',
    ],

    /*
    |--------------------------------------------------------------------------
    | Session sécurisée
    |--------------------------------------------------------------------------
    */

    'session' => [
        'regenerate_on_login' => true,
        'invalidate_on_logout' => true,
        'timeout_inactive' => 30, // minutes
        'concurrent_sessions_limit' => 3,
    ],

    /*
    |--------------------------------------------------------------------------
    | IP Whitelisting/Blacklisting
    |--------------------------------------------------------------------------
    */

    'ip_filtering' => [
        'enabled' => false,
        'whitelist' => [
            // Ajouter des IPs de confiance ici
        ],
        'blacklist' => [
            // IPs bloquées
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Webhook Security
    |--------------------------------------------------------------------------
    */
    'webhook' => [
        'ip_whitelist_enabled' => env('WEBHOOK_IP_WHITELIST_ENABLED', false),
        'allowed_ips' => [
            // IPs Stripe (à configurer selon vos besoins)
            // '54.187.174.169',
            // '54.187.205.235',
            // '54.187.216.72',
        ],
    ],
];
