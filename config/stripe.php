<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Stripe API Keys
    |--------------------------------------------------------------------------
    |
    | Les clés API Stripe pour votre application.
    | Utilisez les clés de test pour le développement et les clés live pour la production.
    |
    */

    'publishable_key' => env('STRIPE_PUBLISHABLE_KEY'),
    'secret_key' => env('STRIPE_SECRET_KEY'),
    'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | Stripe Configuration
    |--------------------------------------------------------------------------
    */

    'currency' => env('STRIPE_CURRENCY', 'eur'),
    'payment_method_types' => ['card'],
    
    /*
    |--------------------------------------------------------------------------
    | Frais et commissions
    |--------------------------------------------------------------------------
    */
    
    'fees' => [
        // Frais Stripe standards en Europe
        'percentage' => 1.4, // 1.4% + 0.25€ par transaction réussie
        'fixed' => 0.25,
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuration des payouts (virements)
    |--------------------------------------------------------------------------
    */
    
    'payout_schedule' => [
        'interval' => 'daily', // daily, weekly, monthly
        'delay_days' => 2, // Délai avant virement (minimum 2 jours en Europe)
    ],

    /*
    |--------------------------------------------------------------------------
    | Webhooks
    |--------------------------------------------------------------------------
    */
    
    'webhooks' => [
        'payment_intent.succeeded',
        'payment_intent.payment_failed',
        'charge.dispute.created',
        'payout.paid',
        'payout.failed',
    ],
];
