<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\MondialRelayController;
use App\Http\Controllers\LegalController;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::resource('products', ProductController::class);

// Routes légales
Route::get('/cgv', [LegalController::class, 'cgv'])->name('legal.cgv');
Route::get('/livraisons-retours', [LegalController::class, 'shippingReturns'])->name('legal.shipping-returns');

// Routes panier
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
Route::delete('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');

// Routes checkout
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::get('/checkout/shipping', [CheckoutController::class, 'shipping'])->name('checkout.shipping');
Route::post('/checkout/shipping', [CheckoutController::class, 'shipping']);
Route::get('/checkout/payment', [CheckoutController::class, 'showPayment'])->name('checkout.payment.show');
Route::post('/checkout/payment', [CheckoutController::class, 'payment'])->name('checkout.payment');
Route::post('/checkout/process', [CheckoutController::class, 'processPayment'])->name('checkout.process');
Route::post('/checkout/confirm-payment', [CheckoutController::class, 'confirmPayment'])->name('checkout.confirm-payment');
Route::get('/checkout/success/{order}', [CheckoutController::class, 'success'])->name('checkout.success');

// UNE SEULE route pour les points de livraison
Route::post('/checkout/delivery-points', [CheckoutController::class, 'getDeliveryPoints'])->name('checkout.delivery.points');

// Webhook Stripe
Route::post('/webhook/stripe', [StripeWebhookController::class, 'handle'])->name('stripe.webhook');

// SUPPRIMER ces routes temporaires qui font doublon :
// Route::post('/api/mondial-relay/search', [MondialRelayController::class, 'getRelayPoints'])->withoutMiddleware(['csrf']);

// Routes Mondial Relay (garder seulement celles-ci)
Route::get('/mondial-relay/test', function () {
    return view('mondial-relay.test');
})->name('mondial-relay.test');

Route::get('/mondial-relay/checkout-example', function () {
    return view('mondial-relay.checkout-example');
})->name('mondial-relay.checkout-example');

// Routes profil
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::get('/profile/orders', [ProfileController::class, 'orders'])->name('profile.orders');
    
    // Dashboard admin Mondial Relay
    Route::prefix('dashboard/mondial-relay')->name('dashboard.mondial-relay.')->group(function () {
        Route::get('/', [MondialRelayController::class, 'dashboard'])->name('index');
        Route::get('/labels', [MondialRelayController::class, 'labels'])->name('labels');
        Route::get('/tracking', [MondialRelayController::class, 'tracking'])->name('tracking');
    });
});