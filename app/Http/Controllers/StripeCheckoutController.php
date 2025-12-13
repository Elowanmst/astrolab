<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Services\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderConfirmation;
use Illuminate\Support\Str;

class StripeCheckoutController extends Controller
{
    protected $cart;

    public function __construct(Cart $cart)
    {
        $this->cart = $cart;
    }

    public function checkout()
    {
        return view('stripe.checkout');
    }

    public function handleShipping(Request $request)
    {
        // Validation simple des données de livraison
        $request->validate([
            'shipping_name' => 'required',
            'shipping_email' => 'required|email',
        ]);

        // Sauvegarder les données de livraison dans la session
        session(['checkout_data' => $request->all()]);

        // Procéder au paiement
        return $this->process($request);
    }

    public function process(Request $request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $lineItems = [];
        $cartItems = $this->cart->get();

        // 1. Validation des stocks
        foreach ($cartItems as $item) {
            $product = Product::find($item['product_id']);
            if (!$product) {
                return redirect()->route('cart.index')->with('error', "Le produit {$item['name']} n'existe plus.");
            }
            
            // Vérifier le stock pour la taille spécifique
            $availableStock = $product->getStockForSize($item['size']);
            if ($availableStock < $item['quantity']) {
                return redirect()->route('cart.index')->with('error', "Stock insuffisant pour {$item['name']} (Taille: {$item['size']}).");
            }

            $lineItems[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $item['name'] . ' (Taille: ' . $item['size'] . ')',
                    ],
                    'unit_amount' => (int) round($item['price'] * 100),
                ],
                'quantity' => $item['quantity'],
            ];
        }

        // Ajouter les frais de port
        $shippingMethod = session('checkout_data.shipping_method', 'home');
        $shippingCost = $this->cart->getShippingCost($shippingMethod);
        
        // Fallback si le prix est 0
        if ($shippingCost == 0) {
            if ($shippingMethod === 'home') {
                $shippingCost = 6.99;
            } elseif ($shippingMethod === 'pickup') {
                $shippingCost = 4.99;
            }
        }
        
        if ($shippingCost > 0) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => 'Frais de livraison',
                    ],
                    'unit_amount' => (int) round($shippingCost * 100),
                ],
                'quantity' => 1,
            ];
        }

        if (empty($lineItems)) {
            return redirect()->route('cart.index')->with('error', 'Votre panier est vide.');
        }

        // 2. Création de la commande en statut "pending"
        $checkoutData = session('checkout_data');
        $totalAmount = $this->cart->getTotal() + $shippingCost;

        $order = Order::create([
            'user_id' => $request->user()?->id, // Null si invité
            'order_number' => 'CMD-' . strtoupper(Str::random(10)), // Temporaire, peut être amélioré
            'status' => 'pending', // En attente de paiement
            'total_amount' => $totalAmount,
            'shipping_name' => $checkoutData['shipping_name'],
            'shipping_email' => $checkoutData['shipping_email'],
            'shipping_phone' => $checkoutData['shipping_phone'] ?? null,
            'shipping_address' => $checkoutData['shipping_address'] ?? 'N/A', // Adapter selon vos champs
            'shipping_city' => $checkoutData['shipping_city'] ?? 'N/A',
            'shipping_postal_code' => $checkoutData['shipping_postal_code'] ?? 'N/A',
            'shipping_country' => 'FR', // Par défaut ou dynamique
            'shipping_method' => $shippingMethod,
            // Ajouter les infos point relais si nécessaire
        ]);

        // Création des items de commande
        foreach ($cartItems as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'product_name' => $item['name'],
                'product_price' => $item['price'],
                'quantity' => $item['quantity'],
                'size' => $item['size'],
                'color' => $item['color'] ?? null,
            ]);
        }

        // 3. Création de la session Stripe avec référence à la commande
        $session = Session::create([
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => route('stripe.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('stripe.cancel'),
            'customer_email' => $checkoutData['shipping_email'],
            'client_reference_id' => $order->id, // Lien crucial pour le webhook
            'metadata' => [
                'order_id' => $order->id,
                'order_number' => $order->order_number
            ],
        ]);

        return redirect($session->url);
    }

    public function success(Request $request)
    {
        $sessionId = $request->query('session_id');
        
        if (!$sessionId) {
            return redirect()->route('home');
        }

        Stripe::setApiKey(config('services.stripe.secret'));
        
        try {
            $session = Session::retrieve($sessionId);
            
            // Vérifier si le paiement est bien "paid"
            if ($session->payment_status !== 'paid') {
                return redirect()->route('cart.index')->with('error', 'Le paiement n\'a pas été confirmé.');
            }

            // Récupérer la commande
            $orderId = $session->client_reference_id;
            $order = Order::find($orderId);

            if (!$order) {
                // Cas rare : commande introuvable
                return redirect()->route('home')->with('error', 'Commande introuvable.');
            }

            // Si la commande est déjà payée, on affiche juste la vue (évite les doublons si rechargement)
            if ($order->status === 'paid') {
                return view('stripe.success', ['order' => $order]);
            }

            // 4. Mise à jour de la commande
            $order->update([
                'status' => 'processing', // Passage en "En traitement" (payé)
                // On pourrait stocker l'ID de transaction Stripe ici
            ]);

            // 5. Décrémentation des stocks
            foreach ($order->items as $item) {
                $product = Product::find($item->product_id);
                if ($product) {
                    $product->decreaseStock($item->size, $item->quantity);
                }
            }

            // 6. Envoi des emails
            try {
                Mail::to($order->shipping_email)->send(new OrderConfirmation($order));
                // Mail::to(config('mail.admin_address'))->send(new NewOrderNotification($order));
            } catch (\Exception $e) {
                // Log l'erreur mais ne pas bloquer le processus
                \Log::error('Erreur envoi email commande ' . $order->id . ': ' . $e->getMessage());
            }

            // 7. Vider le panier
            $this->cart->clear();

            return view('stripe.success', ['order' => $order]);

        } catch (\Exception $e) {
            return redirect()->route('cart.index')->with('error', 'Erreur lors de la vérification du paiement: ' . $e->getMessage());
        }
    }

    public function cancel()
    {
        return view('stripe.cancel');
    }
}
