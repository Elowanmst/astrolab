<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MondialRelayController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Routes API Mondial Relay - SANS DOUBLONS
Route::prefix('mondial-relay')->group(function () {
    Route::post('/relay-points', [MondialRelayController::class, 'getRelayPoints']);
    Route::post('/create-relay-expedition', [MondialRelayController::class, 'createRelayExpedition']);
    Route::post('/create-home-delivery', [MondialRelayController::class, 'createHomeDelivery']);
    Route::post('/track-package', [MondialRelayController::class, 'trackPackage']);
    
    // UNE SEULE route test-connection
    Route::get('/test-connection', [MondialRelayController::class, 'testConnection'])->name('mondial-relay.test');
    
    Route::post('/checkout-delivery-points', [MondialRelayController::class, 'getCheckoutDeliveryPoints'])->name('checkout.delivery.points.api');
    Route::post('/point-details', [MondialRelayController::class, 'getPointDetails'])->name('point.details');
});