<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Session;

class Cart
{
    protected $key = 'cart';

    public function get()
    {
        return Session::get($this->key, []);
    }

    public function add(Product $product, int $quantity = 1, $size = null, $color = null)
    {
        // Vérifier que la taille est fournie
        if (!$size) {
            throw new \InvalidArgumentException('La taille est obligatoire');
        }

        // Vérifier le stock disponible pour cette taille
        $availableStock = $product->getStockForSize($size);
        if ($availableStock <= 0) {
            throw new \Exception('Cette taille n\'est plus en stock');
        }

        $cart = $this->get();
        
        // Créer une clé unique basée sur le produit, la taille et la couleur
        $itemKey = $product->id . '_' . $size . '_' . ($color ?? 'no-color');

        // Calculer la nouvelle quantité totale
        $currentQuantity = isset($cart[$itemKey]) ? $cart[$itemKey]['quantity'] : 0;
        $newTotalQuantity = $currentQuantity + $quantity;

        // Vérifier que la nouvelle quantité ne dépasse pas le stock
        if ($newTotalQuantity > $availableStock) {
            throw new \Exception("Stock insuffisant. Stock disponible: {$availableStock}, quantité demandée: {$newTotalQuantity}");
        }

        if (isset($cart[$itemKey])) {
            $cart[$itemKey]['quantity'] = $newTotalQuantity;
        } else {
            // Récupérer l'URL de l'image avec fallback
            $imageUrl = $product->getFirstMediaUrl('products', 'thumb');
            if (empty($imageUrl) && $product->getMedia('products')->count() > 0) {
                // Fallback vers l'image originale si la thumbnail n'existe pas
                $imageUrl = $product->getFirstMediaUrl('products');
            }
            
            $cart[$itemKey] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => $quantity,
                'size' => $size,
                'color' => $color,
                'image' => $imageUrl,
                'stock_available' => $availableStock,
            ];
        }

        Session::put($this->key, $cart);
    }

    public function remove($itemKey)
    {
        $cart = $this->get();
        unset($cart[$itemKey]);
        Session::put($this->key, $cart);
    }

    public function clear()
    {
        Session::forget($this->key);
    }

    public function total()
    {
        return collect($this->get())->sum(fn($item) => $item['price'] * $item['quantity']);
    }

    public function count()
    {
        return collect($this->get())->sum('quantity');
    }

    public function updateQuantity($itemKey, $quantity)
    {
        $cart = $this->get();
        
        if (isset($cart[$itemKey])) {
            if ($quantity <= 0) {
                unset($cart[$itemKey]);
            } else {
                $cart[$itemKey]['quantity'] = $quantity;
            }
            Session::put($this->key, $cart);
        }
    }

    public function getTotalHT()
    {
        return $this->total();
    }

    public function getTotal()
    {
        return $this->getTotalHT();
    }

    public function getShippingCost($shippingMethodCode = 'home')
    {
        $shippingMethod = \App\Models\ShippingMethod::where('code', $shippingMethodCode)
            ->where('is_active', true)
            ->first();
        
        if (!$shippingMethod) {
            // Fallback vers l'ancienne configuration si aucune méthode trouvée
            $config = config('payment.shipping.methods.' . $shippingMethodCode);
            if (!$config) {
                return 0;
            }
            
            $totalHT = $this->getTotalHT();
            if ($totalHT >= $config['free_above']) {
                return 0;
            }
            return $config['price'];
        }
        
        return $shippingMethod->calculatePrice($this->getTotalHT());
    }

    public function isShippingFree($shippingMethodCode = 'home')
    {
        return $this->getShippingCost($shippingMethodCode) == 0;
    }

    public function getAvailableShippingMethods()
    {
        return \App\Models\ShippingMethod::active()->ordered()->get();
    }

    public function getFinalTotal($shippingMethod = 'home')
    {
        return $this->getTotal() + $this->getShippingCost($shippingMethod);
    }

    public function isEmpty()
    {
        return empty($this->get());
    }
}