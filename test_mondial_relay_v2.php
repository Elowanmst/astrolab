<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== TEST MONDIAL RELAY V1/V2 PRODUCTION ===\n\n";

try {
    $service = new \App\Services\MondialRelayService();
    
    echo "✅ Service initialisé\n";
    
    // Test connexion basique
    echo "\n🔍 Test connexion...\n";
    $testConnection = $service->testConnection();
    echo "Résultat: " . ($testConnection['success'] ? '✅ OK' : '❌ ÉCHEC') . "\n";
    echo "Message: " . $testConnection['message'] . "\n";
    echo "Points trouvés: " . $testConnection['points_found'] . "\n";
    
    // Test checkout avec Paris
    echo "\n🛒 Test checkout Paris 75001...\n";
    $checkoutResult = $service->getCheckoutDeliveryPoints('75001', 'Paris', 10, 15);
    
    echo "Succès: " . ($checkoutResult['success'] ? '✅ OUI' : '❌ NON') . "\n";
    echo "Points trouvés: " . count($checkoutResult['points'] ?? []) . "\n";
    
    if (!empty($checkoutResult['points'])) {
        echo "\nPremiers points:\n";
        foreach (array_slice($checkoutResult['points'], 0, 3) as $i => $point) {
            echo ($i + 1) . ". " . $point['name'] . " (" . $point['type'] . ") - " . $point['distance_text'] . "\n";
            echo "   " . $point['address'] . "\n";
        }
        
        if (isset($checkoutResult['stats'])) {
            echo "\nStatistiques:\n";
            echo "- Points relais (REL): " . $checkoutResult['stats']['relay_points'] . "\n";
            echo "- Lockers (LOC): " . $checkoutResult['stats']['lockers'] . "\n";
        }
    }
    
    // Test avec Blain
    echo "\n🌊 Test checkout Blain 44130...\n";
    $blainResult = $service->getCheckoutDeliveryPoints('44130', 'Blain', 15, 15);
    
    echo "Succès: " . ($blainResult['success'] ? '✅ OUI' : '❌ NON') . "\n";
    echo "Points trouvés: " . count($blainResult['points'] ?? []) . "\n";
    
    if (!empty($blainResult['points'])) {
        echo "Premier point Blain: " . $blainResult['points'][0]['name'] . " (" . $blainResult['points'][0]['type'] . ")\n";
    }
    
} catch (\Exception $e) {
    echo "❌ ERREUR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== FIN DU TEST ===\n";
