<?php

namespace App\Traits;

use App\Services\MondialRelayService;
use Illuminate\Http\JsonResponse;

/**
 * Trait pour faciliter l'intégration de Mondial Relay dans les contrôleurs
 * Spécialement conçu pour les fonctionnalités de checkout
 */
trait HasMondialRelayCheckout
{
    /**
     * Récupérer les points de livraison pour un code postal donné
     */
    protected function getDeliveryPointsForCheckout(string $postalCode, string $city = '', int $radius = 15): array
    {
        $mondialRelayService = app(MondialRelayService::class);
        
        return $mondialRelayService->getCheckoutDeliveryPoints(
            $postalCode,
            $city,
            $radius,
            30 // Limite fixe pour le checkout
        );
    }

    /**
     * Valider qu'un point de livraison existe et est sélectionnable
     */
    protected function validateDeliveryPoint(string $pointId, string $postalCode): bool
    {
        $result = $this->getDeliveryPointsForCheckout($postalCode);
        
        if (!$result['success']) {
            return false;
        }
        
        foreach ($result['points']['all'] as $point) {
            if ($point['id'] === $pointId && $point['selectable']) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Récupérer les informations d'un point spécifique
     */
    protected function getDeliveryPointInfo(string $pointId, string $postalCode): ?array
    {
        $result = $this->getDeliveryPointsForCheckout($postalCode);
        
        if (!$result['success']) {
            return null;
        }
        
        foreach ($result['points']['all'] as $point) {
            if ($point['id'] === $pointId) {
                return $point;
            }
        }
        
        return null;
    }

    /**
     * Calculer le coût total de livraison (produits + livraison)
     */
    protected function calculateTotalShippingCost(string $pointId, string $postalCode, float $productsCost = 0): array
    {
        $pointInfo = $this->getDeliveryPointInfo($pointId, $postalCode);
        
        if (!$pointInfo) {
            return [
                'success' => false,
                'error' => 'Point de livraison introuvable'
            ];
        }
        
        $shippingCost = $pointInfo['delivery_cost'];
        $totalCost = $productsCost + $shippingCost;
        
        return [
            'success' => true,
            'products_cost' => $productsCost,
            'shipping_cost' => $shippingCost,
            'total_cost' => $totalCost,
            'delivery_point' => $pointInfo,
            'currency' => 'EUR'
        ];
    }

    /**
     * Formater les données de livraison pour la commande
     */
    protected function formatDeliveryDataForOrder(string $pointId, string $postalCode): array
    {
        $pointInfo = $this->getDeliveryPointInfo($pointId, $postalCode);
        
        if (!$pointInfo) {
            return [];
        }
        
        return [
            'delivery_method' => 'mondial_relay',
            'delivery_point_id' => $pointInfo['id'],
            'delivery_point_num' => $pointInfo['num'],
            'delivery_point_name' => $pointInfo['name'],
            'delivery_point_address' => $pointInfo['full_address'],
            'delivery_point_type' => $pointInfo['type'],
            'delivery_point_type_label' => $pointInfo['type_label'],
            'delivery_cost' => $pointInfo['delivery_cost'],
            'delivery_time' => $pointInfo['delivery_time'],
            'delivery_phone' => $pointInfo['phone'] ?? '',
            'delivery_coordinates' => [
                'latitude' => $pointInfo['latitude'],
                'longitude' => $pointInfo['longitude']
            ]
        ];
    }

    /**
     * Réponse JSON standardisée pour les erreurs de livraison
     */
    protected function deliveryErrorResponse(string $message, int $statusCode = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'error' => $message,
            'delivery_available' => false
        ], $statusCode);
    }

    /**
     * Réponse JSON standardisée pour les succès de livraison
     */
    protected function deliverySuccessResponse(array $data): JsonResponse
    {
        return response()->json([
            'success' => true,
            'delivery_available' => true,
            ...$data
        ]);
    }
}
