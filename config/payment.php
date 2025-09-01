<?php

return [
    
    /*
    |--------------------------------------------------------------------------
    | Configuration des paiements
    |--------------------------------------------------------------------------
    |
    | Configuration pour les différents processeurs de paiement disponibles
    |
    */

    'default_processor' => env('PAYMENT_PROCESSOR', 'stripe'),
    
    'processors' => [
        
        'stripe' => [
            'name' => 'Stripe',
            'description' => 'Processeur de paiement international',
            'enabled' => env('STRIPE_ENABLED', true),
            'public_key' => env('STRIPE_PUBLISHABLE_KEY'),
            'secret_key' => env('STRIPE_SECRET_KEY'),
            'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
        ],
        
        'paypal' => [
            'name' => 'PayPal',
            'description' => 'PayPal Express Checkout',
            'enabled' => env('PAYPAL_ENABLED', false),
            'client_id' => env('PAYPAL_CLIENT_ID'),
            'client_secret' => env('PAYPAL_CLIENT_SECRET'),
            'sandbox' => env('PAYPAL_SANDBOX', true),
        ],
        
        'lyra' => [
            'name' => 'Lyra (PayZen)',
            'description' => 'Processeur français',
            'enabled' => env('LYRA_ENABLED', false),
            'shop_id' => env('LYRA_SHOP_ID'),
            'key_test' => env('LYRA_KEY_TEST'),
            'key_prod' => env('LYRA_KEY_PROD'),
            'endpoint' => env('LYRA_ENDPOINT', 'https://api.payzen.eu'),
        ],
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Frais et commissions
    |--------------------------------------------------------------------------
    */
    
    'fees' => [
        'stripe' => [
            'percentage' => 2.9, // %
            'fixed' => 0.30, // € par transaction
        ],
        'paypal' => [
            'percentage' => 3.4, // %
            'fixed' => 0.35, // € par transaction
        ],
        'lyra' => [
            'percentage' => 2.5, // %
            'fixed' => 0.25, // € par transaction
        ],
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Configuration des livraisons
    |--------------------------------------------------------------------------
    */
    
    'shipping' => [
        'free_threshold' => 50.00, // Livraison gratuite à partir de X€
        'methods' => [
            'home' => [
                'name' => 'Livraison à domicile',
                'price' => 4.90,
                'free_above' => 50.00,
            ],
            'pickup' => [
                'name' => 'Retrait en magasin',
                'price' => 0.00,
                'free_above' => 0.00,
            ],
        ],
    ],
    
];
