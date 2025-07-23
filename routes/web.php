<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CheckoutController;

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
Route::post('/checkout/payment', [CheckoutController::class, 'payment'])->name('checkout.payment');
Route::post('/checkout/process', [CheckoutController::class, 'processPayment'])->name('checkout.process');
Route::get('/checkout/success/{order}', [CheckoutController::class, 'success'])->name('checkout.success');

// Routes de profil utilisateur (protégées par l'authentification)
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::get('/profile/orders', [ProfileController::class, 'orders'])->name('profile.orders');
});

