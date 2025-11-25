<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductSizeStock;
use App\Enums\ProductSize;

class ProductSizeStockSeeder extends Seeder
{
    /**
     * Migrer les stocks existants vers la nouvelle structure par taille.
     */
    public function run(): void
    {
        // Vider d'abord la table des stocks par taille
        ProductSizeStock::truncate();
        
        // Migrer les stocks existants vers la nouvelle structure
        $products = Product::whereNotNull('stock')->where('stock', '>', 0)->get();
        
        echo "Migration des stocks pour " . $products->count() . " produits...\n";
        
        foreach ($products as $product) {
            // Si le produit a une taille spécifiée, créer le stock pour cette taille
            if ($product->size) {
                ProductSizeStock::create([
                    'product_id' => $product->id,
                    'size' => $product->size,
                    'stock' => $product->stock,
                ]);
                echo "✅ Stock migré pour {$product->name} - Taille {$product->size}: {$product->stock}\n";
            } else {
                // Si pas de taille spécifiée, créer du stock pour la taille M par défaut
                ProductSizeStock::create([
                    'product_id' => $product->id,
                    'size' => ProductSize::M->value,
                    'stock' => $product->stock,
                ]);
                echo "✅ Stock migré pour {$product->name} - Taille M (défaut): {$product->stock}\n";
            }
        }
        
        echo "\n🎉 Migration des stocks terminée avec succès !\n";
    }
}
