<?php

use App\Models\Product;
use App\Models\ProductSizeStock;
use App\Enums\ProductSize;

// Script pour tester la gestion des stocks par taille
echo "🧪 Test de la gestion des stocks par taille\n";
echo "=========================================\n\n";

// Récupérer le premier produit
$product = Product::first();

if (!$product) {
    echo "❌ Aucun produit trouvé\n";
    exit;
}

echo "📦 Produit sélectionné: {$product->name}\n\n";

// Ajouter des stocks pour différentes tailles
$stocks = [
    'XS' => 2,
    'S' => 5,
    'M' => 10,
    'L' => 8,
    'XL' => 3,
    'XXL' => 1
];

echo "📝 Ajout des stocks par taille:\n";
foreach ($stocks as $size => $stock) {
    // Supprimer l'ancien stock s'il existe
    ProductSizeStock::where('product_id', $product->id)
        ->where('size', $size)
        ->delete();
    
    // Créer le nouveau stock
    ProductSizeStock::create([
        'product_id' => $product->id,
        'size' => $size,
        'stock' => $stock
    ]);
    
    echo "   ✅ Taille {$size}: {$stock} en stock\n";
}

echo "\n📊 Résumé des stocks:\n";
echo "   - Stock total: {$product->getTotalStock()}\n";
echo "   - Tailles disponibles: " . implode(', ', $product->getAvailableSizes()) . "\n";

// Tester les méthodes
echo "\n🔍 Tests des méthodes:\n";
echo "   - Stock pour taille M: " . $product->getStockForSize('M') . "\n";
echo "   - Taille XS disponible: " . ($product->isSizeAvailable('XS') ? 'Oui' : 'Non') . "\n";
echo "   - Taille XXS disponible: " . ($product->isSizeAvailable('XXS') ? 'Oui' : 'Non') . "\n";

echo "\n✅ Test terminé avec succès !\n";
echo "Vous pouvez maintenant visiter: http://127.0.0.1:8000/products/{$product->id}\n";
