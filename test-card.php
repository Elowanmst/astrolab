#!/usr/bin/env php
<?php
/**
 * SIMULATEUR DE PAIEMENT AVEC CARTE TEST
 */

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

echo "🃏 TEST PAIEMENT AVEC CARTE STRIPE\n";
echo "==================================\n\n";

try {
    // Configuration Stripe
    \Stripe\Stripe::setApiKey(config('stripe.secret_key'));
    
    echo "💳 Test avec carte 4242424242424242...\n";
    
    // Créer un PaymentIntent avec carte de test
    $paymentIntent = \Stripe\PaymentIntent::create([
        'amount' => 100, // 1€ en centimes
        'currency' => 'eur',
        'description' => 'Test paiement Astrolab',
        'payment_method_data' => [
            'type' => 'card',
            'card' => [
                'number' => '4242424242424242',
                'exp_month' => 12,
                'exp_year' => 2028,
                'cvc' => '123',
            ],
        ],
        'confirm' => true,
        'return_url' => 'https://astrolab.test',
    ]);
    
    echo "✅ PaymentIntent créé: {$paymentIntent->id}\n";
    echo "   Status: {$paymentIntent->status}\n";
    echo "   Montant: " . ($paymentIntent->amount / 100) . "€\n";
    
    if ($paymentIntent->status === 'succeeded') {
        echo "🎉 PAIEMENT RÉUSSI!\n";
        echo "   Le système peut traiter les paiements\n";
    } else {
        echo "⏳ Paiement en cours: {$paymentIntent->status}\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    
    if (strpos($e->getMessage(), 'authentication') !== false) {
        echo "🔑 Problème d'authentification - Vérifiez vos clés Stripe\n";
    }
    
    if (strpos($e->getMessage(), 'card') !== false) {
        echo "💳 Problème de carte - Cela peut être normal en production\n";
    }
}
