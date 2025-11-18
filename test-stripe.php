#!/usr/bin/env php
<?php
/**
 * SCRIPT DE TEST STRIPE AUTOMATISÉ
 * Usage: php test-stripe.php
 */

require_once __DIR__ . '/vendor/autoload.php';

use App\Services\Payment\PaymentService;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

echo "🧪 TEST COMPLET DU SYSTÈME DE PAIEMENT STRIPE\n";
echo "===============================================\n\n";

try {
    // 1. VÉRIFICATIONS PRÉLIMINAIRES
    echo "📋 1. VÉRIFICATIONS CONFIGURATION...\n";
    
    $processor = config('payment.default_processor');
    $stripeKey = config('stripe.secret_key');
    $publishableKey = config('stripe.publishable_key');
    
    echo "✅ Processeur: {$processor}\n";
    echo "✅ Clé secrète: " . substr($stripeKey, 0, 12) . "...\n";
    echo "✅ Clé publique: " . substr($publishableKey, 0, 12) . "...\n";
    
    if (!class_exists('Stripe\Stripe')) {
        throw new Exception("❌ SDK Stripe non installé");
    }
    echo "✅ SDK Stripe installé\n\n";
    
    // 2. TEST CONNEXION STRIPE
    echo "🔗 2. TEST CONNEXION STRIPE...\n";
    
    \Stripe\Stripe::setApiKey($stripeKey);
    
    try {
        $account = \Stripe\Account::retrieve();
        echo "✅ Connexion Stripe réussie\n";
        echo "   Compte: {$account->display_name}\n";
        echo "   Email: {$account->email}\n";
        echo "   Pays: {$account->country}\n\n";
    } catch (Exception $e) {
        throw new Exception("❌ Erreur connexion Stripe: " . $e->getMessage());
    }
    
    // 3. CRÉER UNE COMMANDE TEST
    echo "📦 3. CRÉATION COMMANDE TEST...\n";
    
    DB::beginTransaction();
    
    // Créer un utilisateur test ou en prendre un existant
    $testUser = User::firstOrCreate(
        ['email' => 'test@astrolab.com'],
        [
            'name' => 'Test Stripe',
            'email' => 'test@astrolab.com',
            'password' => bcrypt('password123'),
            'email_verified_at' => now(),
        ]
    );
    
    $testOrder = Order::create([
        'order_number' => 'TEST_' . time(),
        'user_id' => $testUser->id,
        'status' => 'pending',
        'payment_status' => 'pending',
        'total_amount' => 13.50,
        'shipping_name' => 'Test Stripe',
        'shipping_email' => 'test@astrolab.com',
        'shipping_address' => '123 Rue Test',
        'shipping_postal_code' => '75000',
        'shipping_city' => 'Paris',
        'shipping_country' => 'France',
        'shipping_method' => 'home',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    
    echo "✅ Commande test créée: #{$testOrder->order_number}\n";
    echo "   Total: {$testOrder->total_amount}€\n\n";
    
    // 4. TEST CRÉATION PAYMENTINTENT
    echo "💳 4. TEST CRÉATION PAYMENTINTENT...\n";
    
    $paymentService = new PaymentService();
    
    $paymentData = ['payment_method' => 'card'];
    $result = $paymentService->processPayment($paymentData, $testOrder);
    
    echo "📊 Résultat PaymentService:\n";
    echo "   Success: " . ($result['success'] ? 'OUI' : 'NON') . "\n";
    echo "   Processor: " . ($result['processor'] ?? 'N/A') . "\n";
    
    if ($result['success']) {
        echo "   Transaction ID: " . $result['transaction_id'] . "\n";
        echo "   Client Secret: " . (isset($result['client_secret']) ? 'PRÉSENT' : 'MANQUANT') . "\n";
        echo "   Status: " . ($result['status'] ?? 'N/A') . "\n";
        
        // 5. VÉRIFIER LE PAYMENTINTENT SUR STRIPE
        echo "\n🔍 5. VÉRIFICATION SUR STRIPE...\n";
        
        try {
            $paymentIntent = \Stripe\PaymentIntent::retrieve($result['transaction_id']);
            echo "✅ PaymentIntent trouvé sur Stripe\n";
            echo "   ID: {$paymentIntent->id}\n";
            echo "   Montant: " . ($paymentIntent->amount / 100) . "€\n";
            echo "   Status: {$paymentIntent->status}\n";
            echo "   Description: {$paymentIntent->description}\n\n";
            
            // 6. TEST SIMULATION CARTE
            echo "🧪 6. TEST SIMULATION CARTE...\n";
            
            if (app()->environment('local')) {
                // Test avec carte 4242
                $testCardData = [
                    'card_number' => '4242424242424242',
                    'payment_method' => 'card'
                ];
                
                $cardResult = $paymentService->processPayment($testCardData, $testOrder);
                
                if ($cardResult['success']) {
                    echo "✅ Test carte simulée réussi\n";
                    echo "   Transaction: {$cardResult['transaction_id']}\n";
                } else {
                    echo "❌ Test carte simulée échoué: {$cardResult['error']}\n";
                }
            } else {
                echo "ℹ️  Mode production - Test carte ignoré\n";
            }
            
        } catch (Exception $e) {
            echo "❌ Erreur vérification Stripe: " . $e->getMessage() . "\n";
        }
    } else {
        echo "❌ Erreur création PaymentIntent: " . $result['error'] . "\n";
    }
    
    DB::rollBack(); // Ne pas sauver la commande test
    echo "\n🧹 Commande test supprimée\n";
    
    echo "\n🎉 RÉSULTAT FINAL\n";
    echo "==================\n";
    
    if ($result['success']) {
        echo "✅ STRIPE FONCTIONNE CORRECTEMENT!\n";
        echo "   Votre système de paiement est opérationnel\n";
        echo "   Vous pouvez accepter des paiements réels\n\n";
        
        echo "🔗 LIENS UTILES:\n";
        echo "   Dashboard Stripe: https://dashboard.stripe.com/payments\n";
        echo "   Logs Laravel: tail -f storage/logs/laravel.log\n";
    } else {
        echo "❌ PROBLÈME DÉTECTÉ!\n";
        echo "   Erreur: " . $result['error'] . "\n";
        echo "   Vérifiez vos clés Stripe et votre configuration\n";
    }
    
} catch (Exception $e) {
    DB::rollBack();
    echo "\n💥 ERREUR CRITIQUE: " . $e->getMessage() . "\n";
    echo "Vérifiez votre configuration et réessayez\n";
    exit(1);
}
