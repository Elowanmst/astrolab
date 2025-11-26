<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductSizeStock;
use App\Enums\ProductSize;

class TestStocksSeeder extends Seeder
{
    /**
     * Ajouter des stocks de test par taille.
     */
    public function run(): void
    {
        echo "🧪 Test de la gestion des stocks par taille\n";
        echo "=========================================\n\n";

        // Récupérer le premier produit
        $product = Product::first();

        if (!$product) {
            echo "❌ Aucun produit trouvé\n";
            return;
        }

        echo "📦 Produit sélectionné: {$product->name}\n\n";

        // Supprimer les anciens stocks
        ProductSizeStock::where('product_id', $product->id)->delete();

        // Ajouter des stocks pour différentes tailles
        $stocks = [
            'XXS' => 0,   // Rupture de stock
            'XS' => 2,    // Stock faible
            'S' => 5,     // Stock faible
            'M' => 10,    // Stock normal (affiché)
            'L' => 80,    // Stock élevé (masqué)
            'XL' => 3,    // Stock faible
            'XXL' => 1    // Stock très faible
        ];

        echo "📝 Ajout des stocks par taille:\n";
        foreach ($stocks as $size => $stock) {
            ProductSizeStock::create([
                'product_id' => $product->id,
                'size' => $size,
                'stock' => $stock
            ]);
            
            $status = $stock === 0 ? '❌ Rupture' : ($stock <= 5 ? '⚠️  Faible' : '✅ Normal');
            echo "   {$status} Taille {$size}: {$stock} en stock\n";
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
    }
}
