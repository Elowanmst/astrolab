<?php


namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\Cart;
use Illuminate\Http\Request;



class CartController extends Controller
{
    protected $cart;

    public function __construct(Cart $cart)
    {
        $this->cart = $cart;
    }

    public function index()
    {
        return view('cart.index', [
            'cartItems' => $this->cart->get(),
            'total' => $this->cart->total()
        ]);
    }

    public function add(Request $request, Product $product)
    {
        $this->cart->add($product, $request->input('quantity', 1));

        return redirect()->back()->with('success', 'Produit ajouté au panier');
    }

    public function remove($id)
    {
        $this->cart->remove($id);
        return redirect()->route('cart.index')->with('success', 'Produit retiré du panier');
    }
}