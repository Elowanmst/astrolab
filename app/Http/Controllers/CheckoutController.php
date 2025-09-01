<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\SecurePaymentRequest;
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
use Illuminate\Support\Facades\Cache;

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
     * Traitement du paiement et création de la commande - VERSION SÉCURISÉE
     */
    public function processPayment(SecurePaymentRequest $request)
    {
        // Enregistrer le début de la tentative de paiement
        session(['payment_start_time' => time()]);
        
        // Log de début avec ID de transaction unique
        $sessionTransactionId = 'TXN_' . uniqid() . '_' . time();
        
        Log::channel('payments')->info('=== DÉBUT PROCESSUS PAIEMENT SÉCURISÉ ===', [
            'session_transaction_id' => $sessionTransactionId,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'user_id' => $request->user()?->id,
            'timestamp' => now(),
        ]);

        try {
            DB::beginTransaction();
            
            // Vérifications préalables de sécurité
            $securityChecks = $this->performSecurityChecks($request);
            if (!$securityChecks['passed']) {
                Log::channel('security')->warning('Paiement bloqué par les contrôles de sécurité', [
                    'session_transaction_id' => $sessionTransactionId,
                    'reason' => $securityChecks['reason'],
                    'ip' => $request->ip(),
                ]);
                
                DB::rollBack();
                return back()->with('error', 'Transaction refusée pour des raisons de sécurité.');
            }

            // Récupération et validation des données de checkout
            $checkoutData = session('checkout_data');
            if (!$checkoutData) {
                Log::channel('security')->error('Tentative de paiement sans données de checkout', [
                    'session_transaction_id' => $sessionTransactionId,
                    'ip' => $request->ip(),
                ]);
                
                DB::rollBack();
                return redirect()->route('checkout.index')->with('error', 'Données de commande manquantes.');
            }

            // Validation du montant en session vs cart
            $expectedTotal = $this->cart->getTotalTTC() + $this->cart->getShippingCost($checkoutData['shipping_method']);
            $sessionTotal = session('expected_total');
            
            if ($sessionTotal && abs($sessionTotal - $expectedTotal) > 0.01) {
                Log::channel('security')->alert('Tentative de manipulation du montant détectée', [
                    'session_transaction_id' => $sessionTransactionId,
                    'expected_total' => $expectedTotal,
                    'session_total' => $sessionTotal,
                    'ip' => $request->ip(),
                ]);
                
                DB::rollBack();
                return back()->with('error', 'Incohérence détectée dans le montant de la commande.');
            }

            // Créer la commande
            $order = $this->createOrder($checkoutData);
            
            Log::channel('payments')->info('Commande créée avec succès', [
                'session_transaction_id' => $sessionTransactionId,
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'amount' => $order->total_amount,
            ]);

            // Préparer les données de paiement sécurisées
            $paymentData = [
                'card_number' => $request->card_number,
                'card_expiry' => $request->card_expiry,
                'card_cvv' => $request->card_cvv,
                'card_name' => $request->card_name,
                'payment_method' => $request->payment_method,
            ];

            // Traiter le paiement avec le service sécurisé
            $paymentResult = $this->paymentService->processPayment($paymentData, $order, $request);
            
            Log::channel('payments')->info('Résultat du traitement de paiement', [
                'session_transaction_id' => $sessionTransactionId,
                'success' => $paymentResult['success'],
                'transaction_id' => $paymentResult['transaction_id'] ?? null,
                'processor' => $paymentResult['processor'] ?? null,
                'risk_score' => $paymentResult['risk_score'] ?? 0,
            ]);

            if (!$paymentResult['success']) {
                // Incrémenter le compteur d'échecs pour cette IP
                $this->incrementFailureCounter($request->ip());
                
                Log::channel('payments')->warning('Paiement refusé', [
                    'session_transaction_id' => $sessionTransactionId,
                    'error' => $paymentResult['error'] ?? 'Erreur inconnue',
                    'security_violations' => $paymentResult['security_violations'] ?? [],
                ]);
                
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

            // Réinitialiser le compteur d'échecs en cas de succès
            $this->resetFailureCounter($request->ip());

            // Envoi des emails (avec gestion d'erreurs)
            $this->sendConfirmationEmails($order);

            // Nettoyage sécurisé
            $this->cleanupAfterPayment();

            DB::commit();
            
            Log::channel('payments')->info('=== PAIEMENT TERMINÉ AVEC SUCCÈS ===', [
                'session_transaction_id' => $sessionTransactionId,
                'order_id' => $order->id,
                'transaction_id' => $paymentResult['transaction_id'],
                'amount' => $order->total_amount,
            ]);

            // Redirection sécurisée
            return redirect()->route('checkout.success', $order->id)->with('success', 'Paiement effectué avec succès !');

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::channel('payments')->error('=== ERREUR CRITIQUE LORS DU PAIEMENT ===', [
                'session_transaction_id' => $sessionTransactionId,
                'error' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString(),
                'ip' => $request->ip(),
            ]);
            
            return back()->with('error', 'Erreur lors du traitement du paiement. Veuillez réessayer.');
        }
    }

    /**
     * Méthodes de sécurité pour le paiement
     */
    private function performSecurityChecks(Request $request): array
    {
        // Vérifier la session
        if (!session()->has('checkout_data')) {
            return ['passed' => false, 'reason' => 'Session checkout manquante'];
        }

        // Vérifier l'IP
        $originalIp = session('original_ip');
        if ($originalIp && $originalIp !== $request->ip()) {
            return ['passed' => false, 'reason' => 'Changement d\'IP détecté'];
        }

        // Vérifier le rate limiting
        $attempts = Cache::get('payment_attempts:' . $request->ip(), 0);
        if ($attempts >= 5) {
            return ['passed' => false, 'reason' => 'Trop de tentatives de paiement'];
        }

        return ['passed' => true];
    }

    private function incrementFailureCounter(string $ip): void
    {
        $key = 'payment_attempts:' . $ip;
        $attempts = Cache::get($key, 0);
        Cache::put($key, $attempts + 1, now()->addHours(24));
    }

    private function resetFailureCounter(string $ip): void
    {
        Cache::forget('payment_attempts:' . $ip);
    }

    private function sendConfirmationEmails(Order $order): void
    {
        try {
            Mail::to($order->shipping_email)->send(new OrderConfirmation($order));
            Log::channel('payments')->info('Email de confirmation client envoyé', ['order_id' => $order->id]);
        } catch (\Exception $e) {
            Log::channel('payments')->error('Erreur envoi email confirmation client', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
        }

        try {
            $adminEmail = config('mail.admin_email', 'admin@astrolab.com');
            Mail::to($adminEmail)->send(new NewOrderNotification($order));
            Log::channel('payments')->info('Email de notification admin envoyé', ['order_id' => $order->id]);
        } catch (\Exception $e) {
            Log::channel('payments')->error('Erreur envoi email notification admin', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    private function cleanupAfterPayment(): void
    {
        // Vider le panier
        $this->cart->clear();
        
        // Supprimer les données de session sensibles
        session()->forget(['checkout_data', 'payment_start_time', 'expected_total']);
        
        Log::channel('payments')->info('Nettoyage post-paiement effectué');
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
