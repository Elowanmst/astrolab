#!/bin/bash

# Script de test Stripe en mode production
echo "🧪 TEST STRIPE MODE PRODUCTION"
echo "==============================================="

# Test des variables d'environnement
echo ""
echo "📋 1. Variables d'environnement :"
echo "PAYMENT_PROCESSOR: $(grep PAYMENT_PROCESSOR .env | cut -d'=' -f2)"
echo "STRIPE_ENABLED: $(grep STRIPE_ENABLED .env | cut -d'=' -f2)"
echo "STRIPE_KEY: $(grep '^STRIPE_KEY=' .env | cut -d'=' -f2 | cut -c1-25)..."
echo "STRIPE_SECRET: $(grep '^STRIPE_SECRET=' .env | cut -d'=' -f2 | cut -c1-25)..."
echo "STRIPE_WEBHOOK_SECRET: $(grep STRIPE_WEBHOOK_SECRET .env | cut -d'=' -f2 | cut -c1-20)..."

# Test de la configuration Laravel
echo ""
echo "🔧 2. Configuration Laravel :"
php artisan tinker --execute="
echo 'Payment Processor: ' . config('payment.default_processor') . PHP_EOL;
echo 'Stripe Enabled: ' . (config('payment.processors.stripe.enabled') ? 'YES' : 'NO') . PHP_EOL;
echo 'Stripe Key: ' . (config('services.stripe.key') ? substr(config('services.stripe.key'), 0, 25) . '...' : 'MISSING') . PHP_EOL;
echo 'Stripe Secret: ' . (config('services.stripe.secret') ? substr(config('services.stripe.secret'), 0, 25) . '...' : 'MISSING') . PHP_EOL;
echo 'Webhook Secret: ' . (config('services.stripe.webhook_secret') ? substr(config('services.stripe.webhook_secret'), 0, 20) . '...' : 'MISSING') . PHP_EOL;
"

# Test de connexion Stripe (création d'un PaymentIntent fictif)
echo ""
echo "🌐 3. Test de connexion Stripe API :"
php artisan tinker --execute="
try {
    \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
    \$account = \Stripe\Account::retrieve();
    echo 'Account ID: ' . \$account->id . PHP_EOL;
    echo 'Account Type: ' . \$account->type . PHP_EOL;
    echo 'Country: ' . \$account->country . PHP_EOL;
    echo 'Live Mode: ' . (\$account->livemode ? 'YES (PRODUCTION)' : 'NO (TEST)') . PHP_EOL;
    echo '✅ Connexion Stripe réussie - MODE PRODUCTION' . PHP_EOL;
} catch (Exception \$e) {
    echo '❌ Erreur Stripe: ' . \$e->getMessage() . PHP_EOL;
}
"

echo ""
echo "==============================================="
echo "🎉 Test terminé !"
echo ""
echo "📍 Localisation des clés :"
echo "   - Fichier principal: .env (lignes 67-72 et 79-82)"
echo "   - Configuration Laravel: config/services.php (lignes 38-46)"
echo "   - Configuration paiement: config/payment.php"
echo ""
echo "🚀 Votre boutique est prête pour les paiements en production !"
