<?php

namespace App\Services;

use Bmwsly\MondialRelayApi\Facades\MondialRelayService as MondialRelayApiFacade;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class MondialRelayService
{
    /**
     * ✅ Rechercher les points relais pour le checkout (HAUT NIVEAU)
     */
    public function findRelayPointsForCheckout(string $postalCode, string $city = '', int $maxResults = 30): array
    {
        $cacheKey = "relay_points_{$postalCode}_{$city}_{$maxResults}";
        
        return Cache::remember($cacheKey, 3600, function () use ($postalCode, $city, $maxResults) {
            try {
                Log::info('Recherche points relais HAUT NIVEAU', ['cp' => $postalCode]);
                
                // ✅ MÉTHODE HAUT NIVEAU RECOMMANDÉE
                $relayPoints = MondialRelayApiFacade::findRelayPointsForShipment(
                    postalCode: $postalCode,
                    weightInGrams: 1000,
                    deliveryMode: '24R',
                    country: 'FR',
                    maxResults: $maxResults
                );
                
                if (!$relayPoints || count($relayPoints) === 0) {
                    throw new \Exception('Aucun point relais trouvé');
                }
                
                Log::info('Points relais trouvés', ['count' => count($relayPoints)]);
                
                // ✅ Formatage simplifié sans horaires
                $points = [];
                foreach ($relayPoints as $relay) {
                    $points[] = [
                        'id' => $relay->number,
                        'num' => $relay->number,
                        'name' => $relay->name,
                        'address' => $relay->getFullAddress(),
                        'postal_code' => $relay->postalCode,
                        'city' => $relay->city,
                        'country' => $relay->country,
                        'latitude' => $relay->latitude ?? 0,
                        'longitude' => $relay->longitude ?? 0,
                        'distance' => $relay->getFormattedDistance(),
                        'phone' => $relay->phone ?? '',
                        'type' => 'REL',
                        'type_label' => 'Point Relais',
                        'delivery_cost' => 3.90,
                        'delivery_time' => '2-3 jours',
                        'google_maps_url' => "https://maps.google.com/?q={$relay->latitude},{$relay->longitude}",
                    ];
                }
                
                return [
                    'success' => true,
                    'points' => $points,
                    'stats' => ['total' => count($points), 'relay_points' => count($points), 'lockers' => 0]
                ];
                
            } catch (\Exception $e) {
                Log::error('Erreur recherche points relais', ['error' => $e->getMessage()]);
                
                return [
                    'success' => false,
                    'error' => $e->getMessage(),
                    'points' => [],
                    'stats' => ['total' => 0, 'relay_points' => 0, 'lockers' => 0]
                ];
            }
        });
    }
    
    /**
     * ✅ Créer une expédition vers un point relais (HAUT NIVEAU)
     */
    public function createRelayExpedition(array $sender, array $recipient, string $relayNumber, int $weightInGrams, string $orderNumber): array
    {
        try {
            Log::info('Création expédition HAUT NIVEAU', ['relay' => $relayNumber]);
            
            // ✅ MÉTHODE HAUT NIVEAU RECOMMANDÉE
            $expedition = MondialRelayApiFacade::createExpeditionWithLabel(
                sender: $sender,
                recipient: $recipient,
                relayNumber: $relayNumber,
                weightInGrams: $weightInGrams,
                deliveryMode: '24R',
                orderNumber: $orderNumber,
                articlesDescription: 'Commande e-commerce'
            );
            
            return [
                'success' => true,
                'expedition_number' => $expedition->expeditionNumber,
                'tracking_url' => $expedition->getTrackingUrl(),
                'label_url_a4' => $expedition->getLabelUrl('A4'),
                'label_url_a5' => $expedition->getLabelUrl('A5'),
                'label_url_10x15' => $expedition->getLabelUrl('10x15'),
            ];
            
        } catch (\Exception $e) {
            Log::error('Erreur création expédition', ['error' => $e->getMessage()]);
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * ✅ Créer une expédition à domicile (HAUT NIVEAU)
     */
    public function createHomeDeliveryExpedition(array $sender, array $recipient, int $weightInGrams, string $orderNumber): array
    {
        try {
            Log::info('Création expédition domicile HAUT NIVEAU', ['order' => $orderNumber]);
            
            // ✅ MÉTHODE HAUT NIVEAU RECOMMANDÉE
            $expedition = MondialRelayApiFacade::createHomeDeliveryExpeditionWithLabel(
                sender: $sender,
                recipient: $recipient,
                weightInGrams: $weightInGrams,
                deliveryMode: '24L',
                orderNumber: $orderNumber,
                articlesDescription: 'Commande e-commerce'
            );
            
            return [
                'success' => true,
                'expedition_number' => $expedition->expeditionNumber,
                'tracking_url' => $expedition->getTrackingUrl(),
                'label_url_a4' => $expedition->getLabelUrl('A4'),
                'label_url_a5' => $expedition->getLabelUrl('A5'),
                'label_url_10x15' => $expedition->getLabelUrl('10x15'),
            ];
            
        } catch (\Exception $e) {
            Log::error('Erreur création expédition domicile', ['error' => $e->getMessage()]);
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * ✅ Suivi de colis (HAUT NIVEAU)
     */
    public function trackPackage(string $expeditionNumber): array
    {
        try {
            // ✅ MÉTHODE HAUT NIVEAU RECOMMANDÉE
            $summary = MondialRelayApiFacade::getPackageStatusSummary($expeditionNumber);
            
            return [
                'success' => true,
                'status' => $summary['status'],
                'is_delivered' => $summary['is_delivered'],
                'tracking_url' => $summary['tracking_url'],
                'latest_event' => $summary['latest_event']
            ];
            
        } catch (\Exception $e) {
            Log::error('Erreur suivi colis', ['error' => $e->getMessage()]);
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}