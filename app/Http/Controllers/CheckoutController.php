<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\OrderConfirmation;
use App\Mail\NewOrderNotification;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentTransaction;
use App\Models\User;
use App\Services\Cart;
use App\Services\Payment\PaymentService;
use App\Services\MondialRelayService;
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
    protected $mondialRelayService;

    public function __construct(Cart $cart, PaymentService $paymentService, MondialRelayService $mondialRelayService)
    {
        $this->cart = $cart;
        $this->paymentService = $paymentService;
        $this->mondialRelayService = $mondialRelayService;
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
            'total' => $this->cart->getTotal(),
        ]);
    }

    /**
     * Étape 2 : Informations de livraison et mode de livraison
     */
public function shipping(Request $request)
{
    // Si le panier est vide, on redirige
    if ($this->cart->isEmpty()) {
        return redirect()->route('cart.index')->with('error', 'Votre panier est vide.');
    }

    // Vérifier si l'utilisateur est connecté mais que son email n'est pas vérifié
    if (Auth::check() && !Auth::user()->hasVerifiedEmail()) {
        return redirect()->route('verification.notice')
            ->with('message', 'Veuillez vérifier votre adresse email avant de continuer votre commande.');
    }

    // Si c'est un POST depuis la page précédente
    if ($request->isMethod('post')) {
        if ($request->checkout_type === 'register') {
            // Créer le compte et connecter l'utilisateur
            $user = $this->createAccount($request);
            Auth::login($user);
            
            // Rediriger vers la vérification email après création du compte
            return redirect()->route('verification.notice')
                ->with('message', 'Compte créé avec succès ! Veuillez vérifier votre email pour continuer.');
        } elseif ($request->checkout_type === 'login') {
            // Authentifier l'utilisateur existant
            $this->attemptLogin($request);
            
            // Vérifier l'email après connexion
            if (!Auth::user()->hasVerifiedEmail()) {
                return redirect()->route('verification.notice')
                    ->with('message', 'Veuillez vérifier votre adresse email avant de continuer.');
            }
        }

        // Redirection vers la même page en GET pour éviter les problèmes de CSS/JS
        return redirect()->route('checkout.shipping');
    }

    // GET : afficher la page shipping
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
            'selected_relay_point' => 'nullable|string', // Point relais sélectionné (JSON)
        ]);

        // Validation spécifique pour le point relais
        if ($validatedData['shipping_method'] === 'pickup' && empty($validatedData['selected_relay_point'])) {
            return back()->withErrors(['selected_relay_point' => 'Veuillez sélectionner un point relais.'])->withInput();
        }

        // Traitement des données du point relais si nécessaire
        if ($validatedData['shipping_method'] === 'pickup' && !empty($validatedData['selected_relay_point'])) {
            try {
                Log::info('🔍 Raw relay point data:', ['data' => $validatedData['selected_relay_point']]);
                
                $relayPointData = json_decode($validatedData['selected_relay_point'], true);
                Log::info('🔍 Parsed relay point data:', ['parsed' => $relayPointData]);
                
                if ($relayPointData) {
                    // Ajouter les données du point relais aux données validées
                    $validatedData['relay_point_id'] = $relayPointData['id'] ?? '';
                    $validatedData['relay_point_name'] = $relayPointData['name'] ?? '';
                    $validatedData['relay_point_address'] = $relayPointData['address'] ?? '';
                    $validatedData['relay_point_postal_code'] = $relayPointData['postal_code'] ?? $relayPointData['postalCode'] ?? '';
                    $validatedData['relay_point_city'] = $relayPointData['city'] ?? '';
                    
                    Log::info('✅ Relay point data processed:', [
                        'id' => $validatedData['relay_point_id'],
                        'name' => $validatedData['relay_point_name'],
                        'address' => $validatedData['relay_point_address'],
                        'postal_code' => $validatedData['relay_point_postal_code'],
                        'city' => $validatedData['relay_point_city']
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Erreur parsing relay point data', ['error' => $e->getMessage()]);
            }
        }

        // Stocker les données de livraison en session
        session(['checkout_data' => $validatedData]);

        $shippingCost = $this->cart->getShippingCost($validatedData['shipping_method']);
        $total = $this->cart->getTotal();
        $finalTotal = $total + $shippingCost;

        return view('checkout.payment', [
            'cart' => $this->cart,
            'shippingData' => $validatedData,
            'shippingCost' => $shippingCost,
            'total' => $total,
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
        $checkoutData = session('checkout_data');
        if (!$checkoutData) {
            return redirect()->route('checkout.shipping')->with('error', 'Veuillez d\'abord remplir les informations de livraison.');
        }

        $total = $this->cart->getTotal();

        // Utiliser la nouvelle vue Stripe
        return view('checkout.payment-stripe', [
            'cart' => $this->cart,
            'checkoutData' => $checkoutData,
            'total' => $total,
        ]);
    }

    /**
     * NOUVELLE VERSION : Traitement du paiement pour créer le PaymentIntent
     */
    public function processPayment(Request $request)
    {
        Log::info('=== CRÉATION PAYMENTINTENT ===', [
            'request' => $request->all(),
        ]);

        // Validation basique pour la création du PaymentIntent
        $request->validate([
            'payment_method' => 'required|in:card',
        ]);

        $checkoutData = session('checkout_data');
        if (!$checkoutData) {
            Log::error('Données checkout manquantes');
            return response()->json([
                'success' => false,
                'error' => 'Données de commande manquantes'
            ], 400);
        }

        try {
            DB::beginTransaction();

            // Créer la commande
            $order = $this->createOrder($checkoutData);
            Log::info('Commande créée pour PaymentIntent', [
                'order_id' => $order->id,
                'total' => $order->total_amount
            ]);

            // Créer le PaymentIntent (sans confirmer) - PCI DSS Compliant
            $paymentResult = $this->paymentService->processStripePayment($order);
            
            Log::info('PaymentIntent créé', $paymentResult);

            if (!$paymentResult['success']) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'error' => $paymentResult['error'] ?? 'Erreur création PaymentIntent'
                ], 400);
            }

            // Sauvegarder l'ordre avec le PaymentIntent
            $order->update([
                'payment_status' => 'pending',
                'payment_method' => 'stripe',
                'transaction_id' => $paymentResult['payment_intent_id'],
                'status' => 'pending',
            ]);

            DB::commit();

            // Retourner le client_secret pour Stripe Elements
            return response()->json([
                'success' => true,
                'client_secret' => $paymentResult['client_secret'],
                'payment_intent_id' => $paymentResult['payment_intent_id'],
                'order_id' => $order->id,
                'message' => 'PaymentIntent créé avec succès'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur création PaymentIntent', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Erreur serveur: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * ANCIENNE VERSION : Traitement du paiement direct (à supprimer après tests)
     */
    public function processPaymentOld(Request $request)
    {
        // Log du début du processus
        Log::info('=== DÉBUT PROCESSUS PAIEMENT ===', [
            'request_data' => $request->except(['card_number', 'card_cvv']),
            'card_last_4' => substr($request->card_number, -4),
        ]);

        $request->validate([
            'payment_method' => 'required|in:card',
            'card_number' => 'required|string',
            'card_expiry' => 'required|string',
            'card_cvv' => 'required|string',
            'card_name' => 'required|string',
        ]);

        $checkoutData = session('checkout_data');
        if (!$checkoutData) {
            Log::error('Données de checkout manquantes dans la session');
            return redirect()->route('checkout.index')->with('error', 'Données de commande manquantes.');
        }

        Log::info('Données checkout récupérées', ['checkout_data' => $checkoutData]);

        try {
            DB::beginTransaction();
            Log::info('Transaction DB démarrée');

            // Créer la commande
            $order = $this->createOrder($checkoutData);
            Log::info('Commande créée', ['order_id' => $order->id, 'order_number' => $order->order_number]);

            // Traiter le paiement
            $paymentData = [
                'card_number' => $request->card_number,
                'card_expiry' => $request->card_expiry,
                'card_cvv' => $request->card_cvv,
                'card_name' => $request->card_name,
                'payment_method' => $request->payment_method,
            ];
            
            Log::info('Début traitement paiement', [
                'order_id' => $order->id,
                'amount' => $order->total_amount,
                'card_last_4' => substr($request->card_number, -4),
            ]);

            // ATTENTION : Cette méthode viola la conformité PCI DSS
            // Redirection vers la nouvelle méthode sécurisée
            Log::error('Tentative d\'utilisation de l\'ancienne méthode non-PCI compliant');
            throw new \Exception('Cette méthode de paiement n\'est plus supportée pour des raisons de sécurité PCI DSS');
            
            Log::info('Résultat du paiement', ['payment_result' => $paymentResult]);

            if (!$paymentResult['success']) {
                Log::warning('Paiement refusé', ['error' => $paymentResult['error'] ?? 'Erreur inconnue']);
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
            
            Log::info('Commande mise à jour après paiement', [
                'order_id' => $order->id,
                'payment_status' => 'paid',
                'transaction_id' => $paymentResult['transaction_id'],
            ]);

            // NOUVELLE FONCTIONNALITÉ : Créer l'étiquette d'expédition avec le package
            $this->createShippingExpedition($order);

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
            Log::info('Panier vidé');

            // Supprimer les données de session
            session()->forget('checkout_data');
            Log::info('Session checkout nettoyée');

            DB::commit();
            Log::info('Transaction DB commitée avec succès');

            Log::info('=== PAIEMENT TERMINÉ AVEC SUCCÈS ===', [
                'order_id' => $order->id,
                'redirect_to' => 'checkout.success'
            ]);

            return redirect()->route('checkout.success', $order->id);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('=== ERREUR LORS DU PAIEMENT ===', [
                'error' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Erreur lors du traitement du paiement: ' . $e->getMessage());
        }
    }

    /**
     * NOUVELLE MÉTHODE : Créer l'expédition avec le package bmwsly
     */
    protected function createShippingExpedition($order)
    {
        try {
            // Données expéditeur (Astrolab)
            $sender = [
                'name' => 'Astrolab',
                'company' => 'Astrolab',
                'address' => '9 rue Georges Brassens ',
                'city' => 'Blain',
                'postal_code' => '44130',
                'country' => 'FR',
                'phone' => '06 88 70 43 31',
                'email' => 'contact@astrolab.com',
            ];

            // Données destinataire
            $recipient = [
                'name' => $order->shipping_name,
                'address' => $order->shipping_address,
                'city' => $order->shipping_city,
                'postal_code' => $order->shipping_postal_code,
                'country' => 'FR',
                'phone' => $order->shipping_phone ?? '0600000000',
                'email' => $order->shipping_email,
            ];

            // Poids estimé (en grammes)
            $weightInGrams = 1000; // 1kg par défaut, à adapter selon vos produits

            if ($order->shipping_method === 'pickup' && $order->relay_point_id) {
                // EXPÉDITION VERS POINT RELAIS (24R)
                Log::info('Création expédition point relais avec package', [
                    'order_id' => $order->id,
                    'relay_point_id' => $order->relay_point_id
                ]);

                $expeditionResult = $this->mondialRelayService->createRelayExpedition(
                    $sender,
                    $recipient,
                    $order->relay_point_id,
                    $weightInGrams,
                    $order->order_number
                );

                if ($expeditionResult['success']) {
                    $order->update([
                        'tracking_number' => $expeditionResult['expedition_number'],
                        'shipping_label_url' => $expeditionResult['label_url_a4'] ?? null,
                    ]);

                    Log::info('Étiquette point relais créée avec succès', [
                        'order_id' => $order->id,
                        'expedition_number' => $expeditionResult['expedition_number'],
                        'tracking_url' => $expeditionResult['tracking_url']
                    ]);
                } else {
                    Log::error('Erreur création expédition point relais', [
                        'order_id' => $order->id,
                        'error' => $expeditionResult['error']
                    ]);
                }

            } elseif ($order->shipping_method === 'home') {
                // EXPÉDITION À DOMICILE (24L)
                Log::info('Création expédition domicile avec package', [
                    'order_id' => $order->id
                ]);

                $homeDeliveryResult = $this->mondialRelayService->createHomeDeliveryExpedition(
                    $sender,
                    $recipient,
                    $weightInGrams,
                    $order->order_number
                );

                if ($homeDeliveryResult['success']) {
                    $order->update([
                        'tracking_number' => $homeDeliveryResult['expedition_number'],
                        'shipping_label_url' => $homeDeliveryResult['label_url_a4'] ?? null,
                    ]);

                    Log::info('Étiquette domicile créée avec succès', [
                        'order_id' => $order->id,
                        'expedition_number' => $homeDeliveryResult['expedition_number'],
                        'tracking_url' => $homeDeliveryResult['tracking_url']
                    ]);
                } else {
                    Log::error('Erreur création expédition domicile', [
                        'order_id' => $order->id,
                        'error' => $homeDeliveryResult['error']
                    ]);
                }
            }

        } catch (\Exception $e) {
            Log::error('Erreur générale création expédition', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
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

    /**
     * API pour récupérer les points relais avec le package
     */
    public function getDeliveryPoints(Request $request)
    {
        $request->validate([
            'postal_code' => 'required|string|size:5',
            'city' => 'nullable|string|max:100',
            'limit' => 'nullable|integer|min:5|max:50'
        ]);

        try {
            Log::info('Recherche points relais checkout avec package', [
                'postal_code' => $request->input('postal_code'),
                'city' => $request->input('city')
            ]);

            // Utiliser le service avec le package bmwsly
            $result = $this->mondialRelayService->findRelayPointsForCheckout(
                $request->input('postal_code'),
                $request->input('city', ''),
                $request->input('limit', 30)
            );

            return response()->json([
                'success' => $result['success'],
                'data' => [
                    'points' => $result['points'],
                    'stats' => $result['stats']
                ],
                'message' => $result['success'] ? 'Points trouvés avec le package' : 'Erreur de recherche'
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur récupération points checkout avec package: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la recherche des points de livraison'
            ], 500);
        }
    }

    // MÉTHODES PRIVÉES INCHANGÉES
    protected function createAccount(Request $request)
    {
        $request->validate([
            'register_name' => 'required|string|max:255',
            'register_email' => 'required|string|email|max:255|unique:users,email',
            'register_password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->register_name,
            'email' => $request->register_email,
            'password' => Hash::make($request->register_password),
            'email_verified_at' => null, // Email non vérifié lors de la création
        ]);

        // Envoyer l'email de vérification
        try {
            $user->sendEmailVerificationNotification();
            Log::info('Email de vérification envoyé', ['user_id' => $user->id, 'email' => $user->email]);
        } catch (\Exception $e) {
            Log::error('Erreur envoi email vérification', [
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $e->getMessage()
            ]);
        }

        return $user;
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
        $finalTotal = $this->cart->getTotal() + $shippingCost;

        // Préparer les données pour le point relais
        $relayPointData = null;
        $relayPointInfo = [];
        
        if ($shippingData['shipping_method'] === 'pickup' && !empty($shippingData['selected_relay_point'])) {
            $relayPointData = json_decode($shippingData['selected_relay_point'], true);
            if ($relayPointData) {
                $relayPointInfo = [
                    'relay_point_id' => $relayPointData['id'],
                    'relay_point_name' => $relayPointData['name'],
                    'relay_point_address' => $relayPointData['address'],
                    'relay_point_postal_code' => $relayPointData['postal_code'],
                    'relay_point_city' => $relayPointData['city'],
                    'relay_point_data' => json_encode($relayPointData),
                ];
            }
        }

        $order = Order::create(array_merge([
            'user_id' => Auth::id(),
            'total_amount' => $finalTotal,
            'shipping_name' => $shippingData['shipping_name'],
            'shipping_email' => $shippingData['shipping_email'],
            'shipping_phone' => $shippingData['shipping_phone'] ?? null,
            'shipping_address' => $shippingData['shipping_address'],
            'shipping_city' => $shippingData['shipping_city'],
            'shipping_postal_code' => $shippingData['shipping_postal_code'],
            'shipping_method' => $shippingData['shipping_method'],
            'status' => 'pending',
        ], $relayPointInfo));

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

    /**
     * NOUVELLE MÉTHODE : Confirmer un paiement Stripe côté serveur
     */
    public function confirmPayment(Request $request)
    {
        Log::info('=== CONFIRMATION PAIEMENT STRIPE ===', [
            'payment_intent_id' => $request->payment_intent_id,
            'payment_method_id' => $request->payment_method_id,
        ]);

        $request->validate([
            'payment_intent_id' => 'required|string',
            'payment_method_id' => 'required|string',
        ]);

        try {
            // Utiliser notre PaymentService PCI-compliant pour confirmer le paiement
            $confirmResult = $this->paymentService->confirmStripePayment($request->payment_intent_id);
            
            Log::info('Résultat confirmation PaymentService', $confirmResult);

            if (!$confirmResult['success']) {
                Log::error('Échec confirmation PaymentService', $confirmResult);
                return response()->json([
                    'success' => false,
                    'error' => $confirmResult['error'] ?? 'Erreur lors de la confirmation'
                ], 400);
            }

            // Trouver la transaction en base
            $transaction = PaymentTransaction::where('transaction_id', $request->payment_intent_id)->first();
            if (!$transaction) {
                Log::error('Transaction non trouvée', ['payment_intent_id' => $request->payment_intent_id]);
                return response()->json([
                    'success' => false,
                    'error' => 'Transaction non trouvée'
                ], 404);
            }

            // Trouver la commande
            $order = Order::find($transaction->order_id);
            if (!$order) {
                Log::error('Commande non trouvée', ['order_id' => $transaction->order_id]);
                return response()->json([
                    'success' => false,
                    'error' => 'Commande non trouvée'
                ], 404);
            }

            // Vérifier le statut du paiement
            if ($confirmResult['status'] === 'succeeded') {
                DB::beginTransaction();

                // Mettre à jour la transaction
                $transaction->update([
                    'status' => 'completed',
                    'processor_response' => array_merge(
                        $transaction->processor_response ?? [],
                        [
                            'confirmed_at' => now()->toISOString(),
                            'final_status' => $confirmResult['status'],
                            'payment_method_id' => $confirmResult['payment_method_id'],
                        ]
                    ),
                ]);

                // Mettre à jour la commande
                $order->update([
                    'payment_status' => 'paid',
                    'payment_method' => 'stripe',
                    'transaction_id' => $confirmResult['payment_intent_id'],
                    'status' => 'confirmed',
                ]);

                // Envoyer les emails
                $this->sendOrderEmails($order);

                // Vider le panier
                $this->cart->clear();
                session()->forget('checkout_data');

                DB::commit();

                Log::info('Paiement confirmé avec succès', [
                    'order_id' => $order->id,
                    'payment_intent_id' => $confirmResult['payment_intent_id'],
                ]);

                return response()->json([
                    'success' => true,
                    'redirect_url' => route('checkout.success', $order->id),
                    'order_id' => $order->id,
                    'message' => 'Paiement confirmé avec succès'
                ]);

            } else {
                // Paiement échoué
                $transaction->update([
                    'status' => 'failed',
                    'failure_reason' => "Statut Stripe: {$confirmResult['status']}",
                ]);

                Log::warning('Paiement non réussi', [
                    'payment_intent_id' => $confirmResult['payment_intent_id'],
                    'status' => $confirmResult['status'],
                ]);

                return response()->json([
                    'success' => false,
                    'error' => "Paiement non réussi. Statut: {$confirmResult['status']}",
                    'status' => $confirmResult['status'],
                ]);
            }

        } catch (\Stripe\Exception\InvalidRequestException $e) {
            Log::error('Erreur PaymentIntent invalide', [
                'error' => $e->getMessage(),
                'payment_intent_id' => $request->payment_intent_id,
            ]);

            return response()->json([
                'success' => false,
                'error' => 'PaymentIntent invalide: ' . $e->getMessage()
            ], 400);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur confirmation paiement', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Erreur serveur: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Envoyer les emails de commande
     */
    private function sendOrderEmails(Order $order)
    {
        try {
            Mail::to($order->shipping_email)->send(new OrderConfirmation($order));
            Log::info('Email confirmation client envoyé', ['order_id' => $order->id]);
        } catch (\Exception $e) {
            Log::error('Erreur email client', ['error' => $e->getMessage()]);
        }

        try {
            $adminEmail = config('mail.admin_email', 'admin@astrolab.com');
            Mail::to($adminEmail)->send(new NewOrderNotification($order));
            Log::info('Email notification admin envoyé', ['order_id' => $order->id]);
        } catch (\Exception $e) {
            Log::error('Erreur email admin', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Page de confirmation de paiement réussi
     */
    public function paymentSuccess($orderId = null)
    {
        $order = null;
        
        if ($orderId) {
            $order = Order::find($orderId);
            
            // Vérifier que la commande existe et que le paiement est confirmé
            if (!$order || $order->payment_status !== 'completed') {
                return redirect()->route('payment.failed')
                    ->with('error', 'Commande introuvable ou paiement non confirmé.');
            }
        }
        
        return view('payment.success', compact('order'));
    }

    /**
     * Page d'échec de paiement
     */
    public function paymentFailed()
    {
        $error = request('error', session('payment_error', 'Une erreur est survenue lors du paiement.'));
        
        return view('payment.failed', compact('error'));
    }
}