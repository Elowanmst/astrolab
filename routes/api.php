<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MondialRelayController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Routes API Mondial Relay
Route::prefix('mondial-relay')->group(function () {
    Route::post('/relay-points', [MondialRelayController::class, 'getRelayPoints']);
    Route::post('/create-relay-expedition', [MondialRelayController::class, 'createRelayExpedition']);
    Route::post('/create-home-delivery', [MondialRelayController::class, 'createHomeDelivery']);
    Route::post('/track-package', [MondialRelayController::class, 'trackPackage']);
    Route::get('/test-connection', [MondialRelayController::class, 'testConnection']);
    
    // Route optimisée pour le checkout
    Route::post('/checkout-delivery-points', [MondialRelayController::class, 'getCheckoutDeliveryPoints'])->name('checkout.delivery.points.api');
    Route::get('/test-connection', [MondialRelayController::class, 'testConnection'])->name('test');
    Route::post('/point-details', [MondialRelayController::class, 'getPointDetails'])->name('point.details');
});
