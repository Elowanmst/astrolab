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

    public function add(Product $product, int $quantity = 1)
    {
        $cart = $this->get();

        if (isset($cart[$product->id])) {
            $cart[$product->id]['quantity'] += $quantity;
        } else {
            $cart[$product->id] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => $quantity,
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
}