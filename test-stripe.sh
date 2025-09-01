#!/bin/bash

# Test de validation Stripe pour Astrolab
echo "🧪 Test complet de la configuration Stripe..."
echo "================================================="

# Test 1: Vérification des variables d'environnement
echo ""
echo "📋 1. Variables d'environnement :"
echo "STRIPE_ENABLED: $(grep STRIPE_ENABLED .env | cut -d'=' -f2)"
echo "STRIPE_KEY: $(grep STRIPE_KEY .env | cut -d'=' -f2 | cut -c1-20)..."
echo "STRIPE_SECRET: $(grep STRIPE_SECRET .env | cut -d'=' -f2 | cut -c1-20)..."
echo "STRIPE_WEBHOOK_SECRET: $(grep STRIPE_WEBHOOK_SECRET .env | cut -d'=' -f2 | cut -c1-15)..."

# Test 2: Configuration Laravel
echo ""
echo "🔧 2. Configuration Laravel :"
php artisan tinker --execute="
echo 'Config Stripe Key: ' . (config('services.stripe.key') ? '✅ OK' : '❌ MISSING') . PHP_EOL;
echo 'Config Stripe Secret: ' . (config('services.stripe.secret') ? '✅ OK' : '❌ MISSING') . PHP_EOL;
echo 'Config Webhook Secret: ' . (config('services.stripe.webhook_secret') ? '✅ OK' : '❌ MISSING') . PHP_EOL;
"

# Test 3: Connexion Stripe API
echo ""
echo "🌐 3. Test de connexion Stripe API :"
response=$(curl -s http://127.0.0.1:8000/test-stripe)
status=$(echo $response | jq -r '.status')
echo "Status: $status"

if [[ $status == *"SUCCESS"* ]]; then
    echo "✅ SUCCÈS - Stripe fonctionne parfaitement !"
    echo "Account ID: $(echo $response | jq -r '.data.account_id')"
    echo "Country: $(echo $response | jq -r '.data.country')"
    echo "Environment: $(echo $response | jq -r '.data.environment')"
    echo "Payment Intent créé: $(echo $response | jq -r '.data.payment_intent_id')"
else
    echo "❌ ERREUR - Problème de configuration Stripe"
    echo $response | jq .
fi

echo ""
echo "================================================="
echo "🎉 Test terminé - Configuration Stripe validée !"
