<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\MondialRelayController;
use App\Http\Controllers\StripeCheckoutController;
use App\Http\Controllers\LegalController;

// Routes Stripe Simple
Route::get('/stripe-checkout', [StripeCheckoutController::class, 'checkout'])->name('stripe.checkout');
Route::post('/stripe-checkout/process', [StripeCheckoutController::class, 'process'])->name('stripe.process');
Route::get('/stripe-checkout/success', [StripeCheckoutController::class, 'success'])->name('stripe.success');
Route::get('/stripe-checkout/cancel', [StripeCheckoutController::class, 'cancel'])->name('stripe.cancel');

// Route pour le formulaire de livraison qui redirige vers le paiement Stripe
Route::post('/checkout/payment', [StripeCheckoutController::class, 'handleShipping'])->name('checkout.payment');



Route::get('/', [HomeController::class, 'index'])->name('home');

Route::resource('products', ProductController::class);

// Routes légales
Route::get('/cgv', [LegalController::class, 'cgv'])->name('legal.cgv');
Route::get('/livraisons-retours', [LegalController::class, 'shippingReturns'])->name('legal.shipping-returns');

// Routes de vérification email
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (\Illuminate\Foundation\Auth\EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/')->with('message', 'Email vérifié avec succès !');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (\Illuminate\Http\Request $request) {
    try {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('message', 'Lien de vérification envoyé !');
    } catch (\Exception $e) {
        return back()->with('error', $e->getMessage());
    }
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// Routes panier
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
Route::delete('/cart/remove/{itemKey}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/update-quantity', [CartController::class, 'updateQuantity'])->name('cart.updateQuantity');

// Routes checkout
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::get('/checkout/shipping', [CheckoutController::class, 'shipping'])->name('checkout.shipping');
Route::post('/checkout/shipping', [CheckoutController::class, 'shipping']);
Route::get('/checkout/success/{order}', [CheckoutController::class, 'success'])->name('checkout.success');

// UNE SEULE route pour les points de livraison
Route::post('/checkout/delivery-points', [CheckoutController::class, 'getDeliveryPoints'])->name('checkout.delivery.points');

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