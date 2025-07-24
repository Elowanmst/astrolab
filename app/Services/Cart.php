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
        $cart = $this->get();
        
        // Créer une clé unique basée sur le produit, la taille et la couleur
        $itemKey = $product->id;
        if ($size || $color) {
            $itemKey .= '_' . ($size ?? 'no-size') . '_' . ($color ?? 'no-color');
        }

        if (isset($cart[$itemKey])) {
            $cart[$itemKey]['quantity'] += $quantity;
        } else {
            $cart[$itemKey] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => $quantity,
                'size' => $size,
                'color' => $color,
                'image' => $product->getFirstMediaUrl('products', 'thumb'),
            ];
        }

        Session::put($this->key, $cart);
    }

    public function remove($productId)
    {
        $cart = $this->get();
        unset($cart[$productId]);
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

    public function getTVA($rate = 0.20)
    {
        return $this->getTotalHT() * $rate;
    }

    public function getTotalTTC($tvaRate = 0.20)
    {
        return $this->getTotalHT() * (1 + $tvaRate);
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

    public function getFinalTotal($shippingMethod = 'home', $tvaRate = 0.20)
    {
        return $this->getTotalTTC($tvaRate) + $this->getShippingCost($shippingMethod);
    }

    public function isEmpty()
    {
        return empty($this->get());
    }
}