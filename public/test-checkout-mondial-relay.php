<?php

require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\MondialRelayService;

echo "🚀 Test de la fonction Mondial Relay optimisée pour le checkout\n";
echo "================================================================\n\n";

$service = app(MondialRelayService::class);

// Test 1: Paris
echo "📍 Test 1: Paris (75001)\n";
$result1 = $service->getCheckoutDeliveryPoints('75001', 'Paris', 15, 10);

if ($result1['success']) {
    echo "✅ Succès - " . $result1['stats']['total'] . " points trouvés\n";
    echo "   📦 Points relais: " . $result1['stats']['relay_points'] . "\n";
    echo "   🔐 Lockers: " . $result1['stats']['lockers'] . "\n";
    echo "   💬 " . $result1['message'] . "\n";
    
    if (!empty($result1['points']['all'])) {
        $firstPoint = $result1['points']['all'][0];
        echo "   🏪 Premier point: " . $firstPoint['name'] . " (" . $firstPoint['type_label'] . ")\n";
        echo "      📍 " . $firstPoint['full_address'] . "\n";
        echo "      💰 " . $firstPoint['delivery_cost'] . "€ - " . $firstPoint['delivery_time'] . "\n";
    }
} else {
    echo "❌ Erreur: " . ($result1['error'] ?? 'Erreur inconnue') . "\n";
}

echo "\n" . str_repeat("-", 50) . "\n\n";

// Test 2: Blain (test avec une petite ville)
echo "📍 Test 2: Blain (44130)\n";
$result2 = $service->getCheckoutDeliveryPoints('44130', 'Blain', 20, 15);

if ($result2['success']) {
    echo "✅ Succès - " . $result2['stats']['total'] . " points trouvés\n";
    echo "   📦 Points relais: " . $result2['stats']['relay_points'] . "\n";
    echo "   🔐 Lockers: " . $result2['stats']['lockers'] . "\n";
    echo "   💬 " . $result2['message'] . "\n";
} else {
    echo "❌ Erreur: " . ($result2['error'] ?? 'Erreur inconnue') . "\n";
}

echo "\n" . str_repeat("-", 50) . "\n\n";

// Test 3: Code postal invalide
echo "📍 Test 3: Code postal invalide (123)\n";
$result3 = $service->getCheckoutDeliveryPoints('123', 'Test');

if (!$result3['success']) {
    echo "✅ Validation OK - Erreur attendue: " . $result3['error'] . "\n";
} else {
    echo "❌ La validation a échoué - devrait rejeter un CP invalide\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "🎯 Tests terminés!\n";
echo "🔗 Voir l'exemple d'intégration: /mondial-relay/checkout-example\n";
echo "📡 API disponible: POST /api/mondial-relay/checkout-delivery-points\n";
echo "🛒 Intégration checkout: POST /checkout/delivery-points\n";
