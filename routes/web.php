<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;

Route::get('/', [HomeController::class, 'index'])->name('home');

// Route::get('/admin', [DashboardController::class, 'index'])->name('admin')->middleware(['auth']);

Route::resource('products', ProductController::class);

