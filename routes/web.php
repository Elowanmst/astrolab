<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CartController;

Route::get('/', [HomeController::class, 'index'])->name('home');

// Landing page
Route::get('/landing', function () {
    return view('landing');
})->name('landing');

// Page de remerciement pour le formulaire de contact
Route::get('/merci', function () {
    return view('merci');
})->name('merci');

// Route::get('/admin', [DashboardController::class, 'index'])->name('admin')->middleware(['auth']);

Route::resource('products', ProductController::class);

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
Route::delete('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');

