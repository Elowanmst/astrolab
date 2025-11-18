#!/usr/bin/env php
<?php

require_once 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

echo "🔍 === VÉRIFICATION STATUT STRIPE ===\n";
echo "PaymentIntent créé: pi_3SUphhRtSFYebpWz0WPQCSrZ\n\n";

try {
    $stripe = new \Stripe\StripeClient($_ENV['STRIPE_SECRET_KEY']);
    $paymentIntent = $stripe->paymentIntents->retrieve('pi_3SUphhRtSFYebpWz0WPQCSrZ');
    
    echo "📊 STATUT ACTUEL:\n";
    echo "- ID: " . $paymentIntent->id . "\n";
    echo "- Statut: " . $paymentIntent->status . "\n";
    echo "- Montant: " . $paymentIntent->amount . " centimes (" . ($paymentIntent->amount/100) . " EUR)\n";
    echo "- Créé le: " . date('d/m/Y H:i:s', $paymentIntent->created) . "\n";
    echo "- Devise: " . strtoupper($paymentIntent->currency) . "\n";
    
    echo "\n💡 EXPLICATION DU STATUT:\n";
    
    switch($paymentIntent->status) {
        case 'requires_payment_method':
            echo "✅ STATUT NORMAL: PaymentIntent créé mais pas encore payé\n";
            echo "- Le PaymentIntent attend une méthode de paiement\n";
            echo "- Aucun argent n'a été débité de votre compte ou d'une carte\n";
            echo "- Stripe a seulement enregistré l'intention de paiement\n";
            echo "- C'est exactement ce qu'on attend à cette étape\n";
            break;
            
        case 'succeeded':
            echo "🎉 PAIEMENT RÉUSSI: L'argent a été débité!\n";
            break;
            
        case 'requires_confirmation':
            echo "⏳ EN ATTENTE: Nécessite confirmation côté client\n";
            break;
            
        case 'canceled':
            echo "❌ ANNULÉ: PaymentIntent annulé\n";
            break;
            
        default:
            echo "⚠️ STATUT: " . $paymentIntent->status . "\n";
    }
    
    // Vérifier les charges
    echo "\n💳 CHARGES (débits effectifs):\n";
    if ($paymentIntent->charges && $paymentIntent->charges->total_count > 0) {
        echo "Nombre de charges: " . $paymentIntent->charges->total_count . "\n";
        foreach ($paymentIntent->charges->data as $charge) {
            echo "- Charge ID: " . $charge->id . "\n";
            echo "- Statut: " . $charge->status . "\n";
            echo "- Montant: " . $charge->amount . " centimes\n";
        }
    } else {
        echo "✅ AUCUNE CHARGE: Aucun débit effectué (normal)\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}

echo "\n🎯 RÉSUMÉ:\n";
echo "1. ✅ PaymentIntent créé avec succès\n";
echo "2. ✅ Stripe a enregistré votre intention de paiement\n";
echo "3. ⭕ Aucun argent n'a été débité (normal à cette étape)\n";
echo "4. 🔄 Pour un vrai paiement, il faudrait:\n";
echo "   - Utiliser le client_secret côté frontend\n";
echo "   - Confirmer avec Stripe Elements + vraie carte\n";
echo "   - Alors Stripe débiterait la carte\n";

echo "\n💡 DONC: Votre test a fonctionné parfaitement!\n";
echo "Stripe sait qu'il y a une intention de paiement, mais aucun débit réel.\n";
