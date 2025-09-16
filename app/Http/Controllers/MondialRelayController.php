<?php

namespace App\Http\Controllers;

use App\Services\MondialRelayService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MondialRelayController extends Controller
{
    public function __construct(
        private MondialRelayService $mondialRelayService
    ) {}
    
    /**
     * Rechercher les points relais pour le checkout
     */
    public function getRelayPoints(Request $request): JsonResponse
    {
        $request->validate([
            'postal_code' => 'required|string|size:5',
            'city' => 'nullable|string|max:100',
            'limit' => 'nullable|integer|min:5|max:50'
        ]);
        
        $result = $this->mondialRelayService->findRelayPointsForCheckout(
            $request->input('postal_code'),
            $request->input('city', ''),
            $request->input('limit', 30)
        );
        
        return response()->json($result);
    }
    
    /**
     * Créer une expédition vers un point relais
     */
    public function createRelayExpedition(Request $request): JsonResponse
    {
        $request->validate([
            'sender' => 'required|array',
            'recipient' => 'required|array',
            'relay_number' => 'required|string',
            'weight_grams' => 'required|integer|min:1',
            'order_number' => 'required|string'
        ]);
        
        $result = $this->mondialRelayService->createRelayExpedition(
            $request->input('sender'),
            $request->input('recipient'),
            $request->input('relay_number'),
            $request->input('weight_grams'),
            $request->input('order_number')
        );
        
        return response()->json($result);
    }
    
    /**
     * Créer une expédition à domicile
     */
    public function createHomeDelivery(Request $request): JsonResponse
    {
        $request->validate([
            'sender' => 'required|array',
            'recipient' => 'required|array',
            'weight_grams' => 'required|integer|min:1',
            'order_number' => 'required|string'
        ]);
        
        $result = $this->mondialRelayService->createHomeDeliveryExpedition(
            $request->input('sender'),
            $request->input('recipient'),
            $request->input('weight_grams'),
            $request->input('order_number')
        );
        
        return response()->json($result);
    }
    
    /**
     * Suivre un colis
     */
    public function trackPackage(Request $request): JsonResponse
    {
        $request->validate([
            'expedition_number' => 'required|string|size:14'
        ]);
        
        $result = $this->mondialRelayService->trackPackage(
            $request->input('expedition_number')
        );
        
        return response()->json($result);
    }
}