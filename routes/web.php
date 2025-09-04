<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\MondialRelayController;

Route::get('/', [HomeController::class, 'index'])->name('home');

// Route::get('/admin', [DashboardController::class, 'index'])->name('admin')->middleware(['auth']);

Route::resource('products', ProductController::class);

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
Route::delete('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');

// Routes de checkout (processus de commande)
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::get('/checkout/shipping', [CheckoutController::class, 'shipping'])->name('checkout.shipping');
Route::post('/checkout/shipping', [CheckoutController::class, 'shipping']);
Route::get('/checkout/payment', [CheckoutController::class, 'showPayment'])->name('checkout.payment.show');
Route::post('/checkout/payment', [CheckoutController::class, 'payment'])->name('checkout.payment');
Route::post('/checkout/process', [CheckoutController::class, 'processPayment'])->name('checkout.process');
Route::get('/checkout/success/{order}', [CheckoutController::class, 'success'])->name('checkout.success');

// Webhook Stripe (sans middleware CSRF)
Route::post('/webhook/stripe', [StripeWebhookController::class, 'handle'])->name('stripe.webhook');

// Routes API Mondial Relay
Route::prefix('api/mondial-relay')->name('mondial-relay.')->group(function () {
    Route::get('/relay-points/search', [MondialRelayController::class, 'searchRelayPoints'])->name('search');
    Route::post('/shipping-label', [MondialRelayController::class, 'createShippingLabel'])->name('label.create');
    Route::get('/tracking/{trackingNumber}', [MondialRelayController::class, 'trackPackage'])->name('tracking');
    Route::get('/test-connection', [MondialRelayController::class, 'testConnection'])->name('test');
});

// Widget Mondial Relay
Route::get('/mondial-relay/widget', [MondialRelayController::class, 'widget'])->name('mondial-relay.widget');

// Routes de profil utilisateur (protégées par l'authentification)
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::get('/profile/orders', [ProfileController::class, 'orders'])->name('profile.orders');
    
    // Dashboard Mondial Relay (admin)
    Route::prefix('dashboard/mondial-relay')->name('dashboard.mondial-relay.')->group(function () {
        Route::get('/', [MondialRelayController::class, 'dashboard'])->name('index');
        Route::get('/labels', [MondialRelayController::class, 'labels'])->name('labels');
        Route::get('/tracking', [MondialRelayController::class, 'tracking'])->name('tracking');
    });
});

