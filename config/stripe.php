<?php

return [
    'publishable_key' => env('STRIPE_PUBLISHABLE_KEY') ?: env('STRIPE_KEY'),
    'secret_key' => env('STRIPE_SECRET_KEY') ?: env('STRIPE_SECRET'),
    'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),

    'currency' => 'eur',
    'payment_method_types' => ['card'],
    
    'fees' => [
        'percentage' => 1.4,
        'fixed' => 0.25,
    ],
];