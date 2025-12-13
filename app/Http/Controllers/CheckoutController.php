<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\OrderConfirmation;
use App\Mail\NewOrderNotification;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Services\Cart;
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
    protected $mondialRelayService;

    public function __construct(Cart $cart, MondialRelayService $mondialRelayService)
    {
        $this->cart = $cart;
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

    // MÉTHODES PRIVÉES

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
}
