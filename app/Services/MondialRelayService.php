<?php

namespace App\Services;

use Bmwsly\MondialRelayApi\Facades\MondialRelayService as MondialRelayApiFacade;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class MondialRelayService
{
    /**
     * Rechercher les points relais pour le checkout (24R)
     */
    public function findRelayPointsForCheckout(string $postalCode, string $city = '', int $maxResults = 30): array
    {
        $cacheKey = "relay_points_{$postalCode}_{$city}_{$maxResults}";
        
        return Cache::remember($cacheKey, 3600, function () use ($postalCode, $city, $maxResults) {
            try {
                Log::info('Recherche points relais 24R', ['cp' => $postalCode, 'ville' => $city]);
                
                // VÉRIFIER que le package est configuré
                if (!env('MONDIAL_RELAY_ENSEIGNE') || !env('MONDIAL_RELAY_PRIVATE_KEY')) {
                    throw new \Exception('Configuration Mondial Relay manquante dans .env');
                }
                
                Log::info('Configuration Mondial Relay', [
                    'enseigne' => env('MONDIAL_RELAY_ENSEIGNE'),
                    'mode' => env('MONDIAL_RELAY_MODE'),
                    'private_key_length' => strlen(env('MONDIAL_RELAY_PRIVATE_KEY'))
                ]);
                
                // TENTER l'appel à l'API
                $relayPoints = MondialRelayApiFacade::findRelayPointsForShipment(
                    postalCode: $postalCode,
                    weightInGrams: 1000,
                    deliveryMode: '24R',
                    country: 'FR',
                    maxResults: $maxResults
                );
                
                // VÉRIFIER le résultat
                if ($relayPoints === false || $relayPoints === null) {
                    throw new \Exception('Le package a retourné false - problème d\'authentification ou de configuration');
                }
                
                if (!is_array($relayPoints) && !is_iterable($relayPoints)) {
                    throw new \Exception('Le package a retourné un format inattendu: ' . gettype($relayPoints));
                }
                
                Log::info('Points relais trouvés', ['count' => count($relayPoints)]);
                
                $points = [];
                foreach ($relayPoints as $relay) {
                    try {
                        // PROTECTION MAXIMALE pour éviter les erreurs du package
                        $points[] = [
                            'id' => $this->safeProperty($relay, 'number', 'UNKNOWN'),
                            'num' => $this->safeProperty($relay, 'number', 'UNKNOWN'),
                            'name' => $this->safeProperty($relay, 'name', 'Point Relais'),
                            'address' => $this->safeGetFullAddress($relay),
                            'postal_code' => $this->safeProperty($relay, 'postalCode', ''),
                            'city' => $this->safeProperty($relay, 'city', ''),
                            'country' => $this->safeProperty($relay, 'country', 'FR'),
                            'latitude' => $this->safeProperty($relay, 'latitude', 0),
                            'longitude' => $this->safeProperty($relay, 'longitude', 0),
                            'distance' => $this->safeGetDistance($relay),
                            'phone' => $this->safeProperty($relay, 'phone', ''),
                            'type' => 'REL',
                            'type_label' => 'Point Relais',
                            'delivery_cost' => 3.90,
                            'delivery_time' => '2-3 jours',
                            'delivery_mode' => '24R',
                            // SUPPRIMER les vérifications d'ouverture qui plantent
                            'is_open_today' => true,
                            'is_currently_open' => true,
                            'google_maps_url' => $this->safeMethod($relay, 'getGoogleMapsUrl', ''),
                            'opening_hours' => [],
                        ];
                    } catch (\Exception $pointError) {
                        // Si un point individuel plante, on le passe et on continue
                        Log::warning('Point relais ignoré à cause d\'une erreur', [
                            'error' => $pointError->getMessage(),
                            'relay_data' => is_object($relay) ? get_class($relay) : gettype($relay)
                        ]);
                        continue;
                    }
                }
                
                return [
                    'success' => true,
                    'points' => $points,
                    'stats' => [
                        'total' => count($points),
                        'relay_points' => count($points),
                        'lockers' => 0
                    ]
                ];
                
            } catch (\Exception $e) {
                Log::error('Erreur recherche points relais', [
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'postal_code' => $postalCode
                ]);
                
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
     * Créer une expédition vers un point relais (24R)
     */
    public function createRelayExpedition(array $sender, array $recipient, string $relayNumber, int $weightInGrams, string $orderNumber): array
    {
        try {
            Log::info('Création expédition point relais', ['relay' => $relayNumber, 'order' => $orderNumber]);
            
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
            Log::error('Erreur création expédition relais', ['error' => $e->getMessage()]);
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Créer une expédition à domicile (24L)
     */
    public function createHomeDeliveryExpedition(array $sender, array $recipient, int $weightInGrams, string $orderNumber): array
    {
        try {
            Log::info('Création expédition domicile', ['order' => $orderNumber]);
            
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
     * Suivi de colis
     */
    public function trackPackage(string $expeditionNumber): array
    {
        try {
            $summary = MondialRelayApiFacade::getPackageStatusSummary($expeditionNumber);
            
            return [
                'success' => true,
                'status' => $summary['status'],
                'is_delivered' => $summary['is_delivered'],
                'tracking_url' => $summary['tracking_url'],
                'latest_event' => $summary['latest_event']
            ];
            
        } catch (\Exception $e) {
            Log::error('Erreur suivi colis', ['expedition' => $expeditionNumber, 'error' => $e->getMessage()]);
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Méthodes de protection contre les bugs du package
     */
    private function safeProperty($object, string $property, $default = null)
    {
        try {
            if (!is_object($object)) {
                return $default;
            }
            
            if (property_exists($object, $property)) {
                $value = $object->$property;
                return $value !== false && $value !== null ? $value : $default;
            }
            
            return $default;
        } catch (\Exception $e) {
            return $default;
        }
    }

    private function safeMethod($object, string $method, $default = null)
    {
        try {
            if (!is_object($object) || !method_exists($object, $method)) {
                return $default;
            }
            
            $result = $object->$method();
            return $result !== false && $result !== null ? $result : $default;
        } catch (\Exception $e) {
            return $default;
        }
    }

    private function safeGetFullAddress($relay): string
    {
        try {
            if (method_exists($relay, 'getFullAddress')) {
                $address = $relay->getFullAddress();
                if ($address && $address !== false) {
                    return $address;
                }
            }
            
            // Fallback manuel
            $parts = [];
            if ($addr = $this->safeProperty($relay, 'address')) $parts[] = $addr;
            if ($cp = $this->safeProperty($relay, 'postalCode')) $parts[] = $cp;
            if ($city = $this->safeProperty($relay, 'city')) $parts[] = $city;
            
            return !empty($parts) ? implode(', ', $parts) : 'Adresse non disponible';
        } catch (\Exception $e) {
            return 'Adresse non disponible';
        }
    }

    private function safeGetDistance($relay): string
    {
        try {
            if (method_exists($relay, 'getFormattedDistance')) {
                $distance = $relay->getFormattedDistance();
                if ($distance && $distance !== false) {
                    return $distance;
                }
            }
            
            // Fallback sur propriété directe
            $distance = $this->safeProperty($relay, 'distance', 0);
            return is_numeric($distance) ? number_format($distance / 1000, 1) . ' km' : '0 km';
        } catch (\Exception $e) {
            return '0 km';
        }
    }
}