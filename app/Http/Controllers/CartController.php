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
        // Récupérer la taille et la couleur depuis la requête
        $size = $request->input('size');
        $color = $request->input('color');
        $quantity = $request->input('quantity', 1);

        try {
            $this->cart->add($product, $quantity, $size, $color);

            return redirect()->back()->with('cart_success', [
                'message' => 'Article bien ajouté au panier !',
                'product_name' => $product->name
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('cart_error', [
                'message' => $e->getMessage()
            ]);
        }
    }

    public function remove($itemKey)
    {
        $this->cart->remove($itemKey);
        return redirect()->route('cart.index')->with('success', 'Produit retiré du panier');
    }

    public function updateQuantity(Request $request)
    {
        $itemKey = $request->input('item_key');
        $quantity = (int) $request->input('quantity');
        
        // Récupérer l'article depuis le panier pour vérifier le stock
        $cart = $this->cart->get();
        if (!isset($cart[$itemKey])) {
            return response()->json(['success' => false, 'message' => 'Article non trouvé dans le panier']);
        }
        
        $item = $cart[$itemKey];
        
        // Récupérer le produit et vérifier le stock actuel
        $product = Product::find($item['product_id']);
        if ($product && isset($item['size'])) {
            $currentStock = $product->getStockForSize($item['size']);
            
            if ($quantity > $currentStock) {
                return response()->json([
                    'success' => false, 
                    'message' => "Stock insuffisant. Stock disponible : {$currentStock}"
                ]);
            }
        }
        
        if ($quantity <= 0) {
            $this->cart->remove($itemKey);
        } else {
            $this->cart->updateQuantity($itemKey, $quantity);
        }
        
        return response()->json(['success' => true]);
    }
}