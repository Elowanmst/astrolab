<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\OrderConfirmation;
use App\Mail\NewOrderNotification;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Services\Cart;
use App\Services\Payment\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    protected $cart;
    protected $paymentService;

    public function __construct(Cart $cart, PaymentService $paymentService)
    {
        $this->cart = $cart;
        $this->paymentService = $paymentService;
    }

    /**
     * Étape 1 : Choix du mode de commande (connecté/invité/créer compte)
     */
    public function index()
    {
        if ($this->cart->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Votre panier est vide.');
        }

        return view('checkout.auth', [
            'cart' => $this->cart,
            'total' => $this->cart->getTotalTTC(),
        ]);
    }

    /**
     * Étape 2 : Informations de livraison et mode de livraison
     */
    public function shipping(Request $request)
    {
        if ($this->cart->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Votre panier est vide.');
        }

        // Gérer la création de compte ou connexion si nécessaire
        $user = null;
        if ($request->checkout_type === 'register') {
            $user = $this->createAccount($request);
            Auth::login($user);
        } elseif ($request->checkout_type === 'login') {
            $this->attemptLogin($request);
            $user = Auth::user();
        }

        return view('checkout.shipping', [
            'cart' => $this->cart,
            'user' => Auth::user(),
        ]);
    }

    /**
     * Étape 3 : Récapitulatif et paiement
     */
    public function payment(Request $request)
    {
        $validatedData = $request->validate([
            'shipping_name' => 'required|string|max:255',
            'shipping_email' => 'required|email|max:255',
            'shipping_phone' => 'required|string|max:20',
            'shipping_address' => 'required|string|max:500',
            'shipping_city' => 'required|string|max:100',
            'shipping_postal_code' => 'required|string|max:10',
            'shipping_method' => 'required|in:home,pickup',
        ]);

        // Stocker les données de livraison en session
        session(['checkout_data' => $validatedData]);

        $shippingCost = $this->cart->getShippingCost($validatedData['shipping_method']);
        $totalHT = $this->cart->getTotalHT();
        $tva = $this->cart->getTVA();
        $totalTTC = $this->cart->getTotalTTC();
        $finalTotal = $totalTTC + $shippingCost;

        return view('checkout.payment', [
            'cart' => $this->cart,
            'shippingData' => $validatedData,
            'shippingCost' => $shippingCost,
            'totalHT' => $totalHT,
            'tva' => $tva,
            'totalTTC' => $totalTTC,
            'finalTotal' => $finalTotal,
        ]);
    }

    /**
     * Affichage de la page de paiement (GET)
     */
    public function showPayment()
    {
        if ($this->cart->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Votre panier est vide.');
        }

        // Vérifier si les données de livraison sont en session
        $shippingData = session('checkout_data');
        if (!$shippingData) {
            return redirect()->route('checkout.shipping')->with('error', 'Veuillez d\'abord remplir les informations de livraison.');
        }

        $shippingCost = $this->cart->getShippingCost($shippingData['shipping_method']);
        $totalHT = $this->cart->getTotalHT();
        $tva = $this->cart->getTVA();
        $totalTTC = $this->cart->getTotalTTC();
        $finalTotal = $totalTTC + $shippingCost;

        return view('checkout.payment', [
            'cart' => $this->cart,
            'shippingData' => $shippingData,
            'shippingCost' => $shippingCost,
            'totalHT' => $totalHT,
            'tva' => $tva,
            'totalTTC' => $totalTTC,
            'finalTotal' => $finalTotal,
        ]);
    }

    /**
     * Traitement du paiement et création de la commande
     */
    public function processPayment(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|in:card',
            'card_number' => 'required|string',
            'card_expiry' => 'required|string',
            'card_cvv' => 'required|string',
            'card_name' => 'required|string',
        ]);

        $checkoutData = session('checkout_data');
        if (!$checkoutData) {
            return redirect()->route('checkout.index')->with('error', 'Données de commande manquantes.');
        }

        try {
            DB::beginTransaction();

            // Créer la commande
            $order = $this->createOrder($checkoutData);

            // Traiter le paiement
            $paymentResult = $this->paymentService->processPayment([
                'card_number' => $request->card_number,
                'card_expiry' => $request->card_expiry,
                'card_cvv' => $request->card_cvv,
                'card_name' => $request->card_name,
                'payment_method' => $request->payment_method,
            ], $order);

            if (!$paymentResult['success']) {
                DB::rollBack();
                return back()->with('error', 'Paiement refusé: ' . ($paymentResult['error'] ?? 'Erreur inconnue'));
            }

            // Mettre à jour la commande avec les informations de paiement
            $order->update([
                'payment_status' => 'paid',
                'payment_method' => $paymentResult['processor'],
                'transaction_id' => $paymentResult['transaction_id'],
                'status' => 'confirmed',
            ]);

            // Envoyer l'email de confirmation au client
            try {
                Mail::to($order->shipping_email)->send(new OrderConfirmation($order));
            } catch (\Exception $e) {
                Log::error('Erreur envoi email confirmation client', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage()
                ]);
            }

            // Envoyer l'email de notification à l'admin
            try {
                $adminEmail = config('mail.admin_email', 'admin@astrolab.com');
                Mail::to($adminEmail)->send(new NewOrderNotification($order));
            } catch (\Exception $e) {
                Log::error('Erreur envoi email notification admin', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage()
                ]);
            }

            // Vider le panier
            $this->cart->clear();

            // Supprimer les données de session
            session()->forget('checkout_data');

            DB::commit();

            return redirect()->route('checkout.success', $order->id);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors du traitement du paiement: ' . $e->getMessage());
        }
    }

    /**
     * Page de succès
     */
    public function success($orderId)
    {
        $order = Order::with('items')->findOrFail($orderId);
        
        // Vérifier que l'utilisateur a accès à cette commande
        if (Auth::check() && $order->user_id !== Auth::id()) {
            abort(403);
        }

        return view('checkout.success', compact('order'));
    }

    protected function createAccount(Request $request)
    {
        $request->validate([
            'register_name' => 'required|string|max:255',
            'register_email' => 'required|string|email|max:255|unique:users,email',
            'register_password' => 'required|string|min:8|confirmed',
        ]);

        return User::create([
            'name' => $request->register_name,
            'email' => $request->register_email,
            'password' => Hash::make($request->register_password),
            'newsletter' => $request->has('register_newsletter'),
        ]);
    }

    protected function attemptLogin(Request $request)
    {
        $request->validate([
            'login_email' => 'required|email',
            'login_password' => 'required',
        ]);

        $credentials = [
            'email' => $request->login_email,
            'password' => $request->login_password,
        ];

        if (!Auth::attempt($credentials)) {
            throw new \Exception('Identifiants incorrects.');
        }
    }

    protected function createOrder($shippingData)
    {
        $shippingCost = $this->cart->getShippingCost($shippingData['shipping_method']);
        $finalTotal = $this->cart->getTotalTTC() + $shippingCost;

        $order = Order::create([
            'user_id' => Auth::id(),
            'total_amount' => $finalTotal,
            'shipping_name' => $shippingData['shipping_name'],
            'shipping_email' => $shippingData['shipping_email'],
            'shipping_address' => $shippingData['shipping_address'],
            'shipping_city' => $shippingData['shipping_city'],
            'shipping_postal_code' => $shippingData['shipping_postal_code'],
            'status' => 'pending',
        ]);

        // Créer les articles de commande
        foreach ($this->cart->get() as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'product_name' => $item['name'],
                'product_price' => $item['price'],
                'quantity' => $item['quantity'],
                'size' => $item['size'] ?? null,
                'color' => $item['color'] ?? null,
            ]);
        }

        return $order;
    }
}
