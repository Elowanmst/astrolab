<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class MondialRelayService
{
    private $soapClient;  // Client SOAP pour V1
    private $restClient;  // Client REST pour V2
    private $config;
    private $v2Token = null;

    public function __construct()
    {
        $this->config = [
            'enabled' => env('MONDIAL_RELAY_ENABLED', true),
            'mode' => env('MONDIAL_RELAY_MODE', 'production'),
            
            // Configuration V1 (SOAP) - Points relais classiques
            'v1' => [
                'api_url' => env('MONDIAL_RELAY_V1_API_URL', 'https://api.mondialrelay.com/WebService.asmx'),
                'wsdl_url' => env('MONDIAL_RELAY_V1_WSDL_URL', 'http://www.mondialrelay.fr/webservice/Web_Services.asmx?WSDL'),
                'enseigne' => env('MONDIAL_RELAY_V1_ENSEIGNE', 'CC235KWE'),
                'private_key' => env('MONDIAL_RELAY_V1_PRIVATE_KEY', '1GixuOdd'),
                'brand' => env('MONDIAL_RELAY_V1_BRAND', 'CC'),
            ],
            
            // Configuration V2 (REST) - Lockers et services modernes
            'v2' => [
                'api_url' => env('MONDIAL_RELAY_V2_API_URL', 'https://connect-api.mondialrelay.com/api'),
                'brand_id' => env('MONDIAL_RELAY_V2_BRAND_ID', 'CC235KWE'),
                'login' => env('MONDIAL_RELAY_V2_LOGIN', 'CC235KWE@business-api.mondialrelay.com'),
                'password' => env('MONDIAL_RELAY_V2_PASSWORD', '&AbLHN5keS9EGYkuMF#a'),
            ]
        ];

        // Client SOAP pour V1 (sera initialisé à la demande)
        $this->soapClient = null;
        
        // Client REST pour V2
        $this->restClient = new Client([
            'base_uri' => $this->config['v2']['api_url'],
            'timeout' => 30,
            'verify' => true,
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ]
        ]);
    }

    /**
     * Rechercher des points relais et lockers
     */
    public function findRelayPoints($params = [], $type = 'all')
    {
        if (!$this->config['enabled']) {
            return $this->mockRelayPoints($params);
        }

        try {
            // Rechercher tous les points (relais + lockers)
            $allResults = $this->searchByType($type, $params);
            
            if (!$allResults['success']) {
                // Fallback sur les données mockées en cas d'erreur API
                $mockResult = $this->mockRelayPoints($params);
                Log::warning('Fallback vers données mockées', ['error' => $allResults['error'] ?? 'Erreur inconnue']);
                return $mockResult;
            }
            
            $points = $allResults['points'];
            
            // Log pour débogage
            $relayCount = count(array_filter($points, fn($p) => $p['type'] === 'REL'));
            $lockerCount = count(array_filter($points, fn($p) => $p['type'] === 'LOC'));
            
            Log::info('Recherche points relais/lockers', [
                'type' => $type,
                'total' => count($points),
                'relay_points' => $relayCount,
                'lockers' => $lockerCount,
                'params' => $params
            ]);

            if (empty($points)) {
                return [
                    'success' => true,
                    'points' => [],
                    'message' => 'Aucun point trouvé dans la zone de recherche',
                    'stats' => [
                        'total' => 0,
                        'relay_points' => 0,
                        'lockers' => 0
                    ]
                ];
            }
            
            // Trier par distance
            usort($points, function($a, $b) {
                return floatval($a['distance']) <=> floatval($b['distance']);
            });

            return [
                'success' => true,
                'points' => $points,
                'stats' => [
                    'total' => count($points),
                    'relay_points' => $relayCount,
                    'lockers' => $lockerCount
                ]
            ];

        } catch (\Exception $e) {
            Log::error('Erreur Mondial Relay findRelayPoints: ' . $e->getMessage());
            
            // En cas d'erreur, retourner des données de test
            return $this->mockRelayPoints($params);
        }
    }

    /**
     * Authentification V2 REST API
     */
    private function authenticateV2()
    {
        if ($this->v2Token) {
            return $this->v2Token;
        }

        try {
            $response = $this->restClient->post('/auth/token', [
                'json' => [
                    'login' => $this->config['v2']['login'],
                    'password' => $this->config['v2']['password']
                ]
            ]);

            $data = json_decode($response->getBody(), true);
            
            if (isset($data['token'])) {
                $this->v2Token = $data['token'];
                return $this->v2Token;
            }
            
            throw new \Exception('Token non reçu dans la réponse V2');
            
        } catch (\Exception $e) {
            Log::error('Erreur authentification V2: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Recherche via API V2 REST (pour lockers)
     */
    private function searchV2PickupPoints($params)
    {
        try {
            $token = $this->authenticateV2();
            
            // Paramètres pour l'API V2
            $v2Params = [
                'country' => $params['Pays'] ?? 'FR',
                'postcode' => $params['CP'] ?? '',
                'city' => $params['Ville'] ?? '',
                'latitude' => $params['Latitude'] ?? null,
                'longitude' => $params['Longitude'] ?? null,
                'radius' => intval($params['RayonRecherche'] ?? 10),
                'limit' => intval($params['NombreResultats'] ?? 50),
                'services' => ['locker'] // Rechercher spécifiquement les lockers
            ];

            $response = $this->restClient->get('/pickup-points', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token
                ],
                'query' => array_filter($v2Params) // Enlever les valeurs nulles
            ]);

            $data = json_decode($response->getBody(), true);
            
            if (!isset($data['data']) || !is_array($data['data'])) {
                return ['success' => false, 'error' => 'Format de réponse V2 invalide'];
            }

            $points = [];
            foreach ($data['data'] as $point) {
                $points[] = [
                    'id' => $point['id'] ?? '',
                    'name' => $point['name'] ?? '',
                    'address' => $point['address'] ?? '',
                    'postal_code' => $point['postcode'] ?? '',
                    'city' => $point['city'] ?? '',
                    'country' => $point['country'] ?? 'FR',
                    'latitude' => $point['latitude'] ?? '',
                    'longitude' => $point['longitude'] ?? '',
                    'distance' => $point['distance'] ?? '',
                    'phone' => $point['phone'] ?? '',
                    'type' => 'LOC',
                    'opening_hours' => $point['opening_hours'] ?? [],
                    // Champs de compatibilité
                    'Num' => $point['id'] ?? '',
                    'Nom' => $point['name'] ?? '',
                    'Adresse' => $point['address'] ?? ''
                ];
            }

            return [
                'success' => true,
                'points' => $points
            ];

        } catch (\Exception $e) {
            Log::error('Erreur recherche V2: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Initialiser le client SOAP V1 (à la demande)
     */
    private function getSoapClient()
    {
        if ($this->soapClient === null) {
            try {
                $this->soapClient = new \SoapClient($this->config['v1']['wsdl_url'], [
                    'trace' => true,
                    'exceptions' => true,
                    'soap_version' => SOAP_1_1,
                    'cache_wsdl' => WSDL_CACHE_NONE
                ]);
            } catch (\Exception $e) {
                Log::error('Erreur initialisation SOAP: ' . $e->getMessage());
                throw $e;
            }
        }
        return $this->soapClient;
    }

    /**
     * Rechercher par type hybride (V1 pour REL, V2 pour LOC)
     */
    private function searchByType($action, $params = [], $rayonKm = 10)
    {
        $defaultParams = [
            'Enseigne' => $this->config['v1']['enseigne'],
            'Pays' => 'FR',
            'Ville' => '',
            'CP' => '',
            'Latitude' => '',
            'Longitude' => '',
            'Taille' => '',
            'Poids' => '1000',
            'DelaiEnvoi' => '0',
            'RayonRecherche' => (string)$rayonKm,
            'NombreResultats' => '50'
        ];

        $searchParams = array_merge($defaultParams, $params);
        $allPoints = [];
        $errors = [];

        try {
            // --- REL : points relais classiques via V1 SOAP WSI4 ---
            if ($action === 'REL' || $action === 'all') {
                Log::info('Recherche points relais REL via V1', ['params' => $searchParams]);
                
                $paramsREL = $searchParams;
                $paramsREL['Action'] = 'REL';
                $paramsREL['Security'] = $this->generateSignature($paramsREL);

                try {
                    $responseREL = $this->soapRequest('WSI4_PointRelais_Recherche', $paramsREL);

                    if ($responseREL && isset($responseREL->WSI4_PointRelais_RechercheResult)) {
                        $parsed = $this->parseRelayPointsResponse($responseREL->WSI4_PointRelais_RechercheResult, 'REL');
                        if ($parsed['success']) {
                            $allPoints = array_merge($allPoints, $parsed['points']);
                            Log::info('Points REL trouvés via V1', ['count' => count($parsed['points'])]);
                        } else {
                            $errors[] = 'REL V1: ' . $parsed['error'];
                        }
                    }
                } catch (\Exception $e) {
                    $errors[] = 'REL V1: ' . $e->getMessage();
                    Log::error("Erreur API REL V1: " . $e->getMessage());
                }
            }

            // --- LOC : lockers via V2 REST API ---
            if ($action === 'LOC' || $action === 'all') {
                Log::info('Recherche lockers LOC via V2', ['cp' => $searchParams['CP'], 'rayon' => $rayonKm]);
                
                try {
                    $lockerResult = $this->searchV2PickupPoints($searchParams);
                    
                    if ($lockerResult['success']) {
                        $allPoints = array_merge($allPoints, $lockerResult['points']);
                        Log::info('Points LOC trouvés via V2', ['count' => count($lockerResult['points'])]);
                    } else {
                        $errors[] = 'LOC V2: ' . $lockerResult['error'];
                        
                        // Fallback sur V1 WSI2 si V2 échoue
                        Log::info('Fallback LOC sur V1 WSI2');
                        $fallbackResult = $this->searchV1LockersWSI2($searchParams, $rayonKm);
                        if ($fallbackResult['success']) {
                            $allPoints = array_merge($allPoints, $fallbackResult['points']);
                            Log::info('Points LOC trouvés via V1 fallback', ['count' => count($fallbackResult['points'])]);
                        }
                    }
                } catch (\Exception $e) {
                    $errors[] = 'LOC V2: ' . $e->getMessage();
                    Log::error("Erreur API LOC V2: " . $e->getMessage());
                    
                    // Fallback sur V1
                    $fallbackResult = $this->searchV1LockersWSI2($searchParams, $rayonKm);
                    if ($fallbackResult['success']) {
                        $allPoints = array_merge($allPoints, $fallbackResult['points']);
                    }
                }
            }

            return [
                'success' => empty($errors) || !empty($allPoints),
                'points' => $allPoints,
                'errors' => $errors
            ];

        } catch (\Exception $e) {
            Log::error('Erreur searchByType: ' . $e->getMessage());
            return [
                'success' => false,
                'points' => [],
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Fallback : recherche lockers via V1 WSI2 (ancienne méthode)
     */
    private function searchV1LockersWSI2($searchParams, $rayonKm)
    {
        try {
            // Générer tous les CP autour du CP demandé pour couvrir le rayon
            if (!empty($searchParams['Latitude']) && !empty($searchParams['Longitude'])) {
                $cpList = $this->generateCPsAroundCoordinates(
                    $searchParams['Latitude'], 
                    $searchParams['Longitude'], 
                    $rayonKm
                );
            } else {
                $cpList = $this->generateCPsAround($searchParams['CP'], $rayonKm);
            }

            $lockerPoints = [];
            foreach ($cpList as $currentCP) {
                $paramsLOC = [
                    'Enseigne' => $this->config['v1']['enseigne'],
                    'Pays' => $searchParams['Pays'],
                    'CP' => $currentCP,
                    'NbResult' => $searchParams['NombreResultats'],
                    'TypeActivite' => '24R'
                ];

                // Générer la signature correcte pour WSI2
                $paramsLOC['Security'] = $this->generateSignatureWSI2($paramsLOC);

                try {
                    $responseLOC = $this->soapRequest('WSI2_PointRelais_Recherche', $paramsLOC);

                    if ($responseLOC && isset($responseLOC->WSI2_PointRelais_RechercheResult)) {
                        $result = $responseLOC->WSI2_PointRelais_RechercheResult;
                        
                        if (isset($result->STAT) && $result->STAT == '0') {
                            if (isset($result->PointsRelais->PointRelais_Details)) {
                                $lockers = $result->PointsRelais->PointRelais_Details;
                                if (!is_array($lockers)) {
                                    $lockers = [$lockers];
                                }

                                foreach ($lockers as $locker) {
                                    $lockerPoints[$locker->Num] = [
                                        'id' => $locker->Num,
                                        'name' => $locker->LgAdr1 ?? '',
                                        'address' => ($locker->LgAdr3 ?? '') . ' ' . ($locker->CP ?? '') . ' ' . ($locker->Ville ?? ''),
                                        'postal_code' => $locker->CP ?? '',
                                        'city' => $locker->Ville ?? '',
                                        'country' => $locker->Pays ?? 'FR',
                                        'latitude' => $locker->Latitude ?? '',
                                        'longitude' => $locker->Longitude ?? '',
                                        'distance' => $locker->Distance ?? '',
                                        'phone' => $locker->Tel ?? '',
                                        'type' => 'LOC',
                                        'opening_hours' => $this->parseOpeningHours($locker),
                                        'Num' => $locker->Num,
                                        'Nom' => $locker->LgAdr1 ?? '',
                                        'Adresse' => ($locker->LgAdr3 ?? '') . ' ' . ($locker->CP ?? '') . ' ' . ($locker->Ville ?? '')
                                    ];
                                }
                            }
                        }
                    }
                } catch (\Exception $e) {
                    Log::warning("Erreur API LOC V1 CP {$currentCP}: " . $e->getMessage());
                }
            }

            return [
                'success' => true,
                'points' => array_values($lockerPoints)
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Méthode checkout optimisée pour la sélection en production
     */
    public function getCheckoutDeliveryPoints($codePostal, $ville = '', $rayonKm = 15, $nombreResultats = 30)
    {
        $cacheKey = "mondial_relay_checkout_{$codePostal}_{$ville}_{$rayonKm}_{$nombreResultats}";
        
        // Cache de 1 heure
        return Cache::remember($cacheKey, 3600, function() use ($codePostal, $ville, $rayonKm, $nombreResultats) {
            
            $params = [
                'CP' => $codePostal,
                'Ville' => $ville,
                'RayonRecherche' => (string)$rayonKm,
                'NombreResultats' => (string)$nombreResultats
            ];

            $result = $this->findRelayPoints($params, 'all');
            
            if (!$result['success'] || empty($result['points'])) {
                return [
                    'success' => false,
                    'points' => [],
                    'message' => 'Aucun point de livraison trouvé dans cette zone'
                ];
            }

            // Formatage spécifique pour le checkout
            $checkoutPoints = [];
            foreach ($result['points'] as $point) {
                $checkoutPoints[] = [
                    'id' => $point['id'],
                    'name' => $point['name'],
                    'type' => $point['type'],
                    'type_label' => $point['type'] === 'REL' ? 'Point Relais' : 'Locker',
                    'address' => $this->formatFullAddress($point),
                    'postal_code' => $point['postal_code'],
                    'city' => $point['city'],
                    'distance' => round(floatval($point['distance'] ?? 0), 1),
                    'distance_text' => round(floatval($point['distance'] ?? 0), 1) . ' km',
                    'phone' => $point['phone'] ?? '',
                    'opening_hours' => $point['opening_hours'] ?? [],
                    'coordinates' => [
                        'latitude' => floatval($point['latitude'] ?? 0),
                        'longitude' => floatval($point['longitude'] ?? 0)
                    ],
                    'delivery_cost' => $this->calculateDeliveryCost($point['type']),
                    'estimated_delivery' => '2-3 jours ouvrés',
                    // Données techniques pour les commandes
                    'relay_data' => [
                        'Num' => $point['Num'] ?? $point['id'],
                        'Nom' => $point['Nom'] ?? $point['name'],
                        'Adresse' => $point['Adresse'] ?? $point['address'],
                        'Type' => $point['type']
                    ]
                ];
            }

            // Trier par distance
            usort($checkoutPoints, function($a, $b) {
                return $a['distance'] <=> $b['distance'];
            });

            // Limiter le nombre de résultats
            $checkoutPoints = array_slice($checkoutPoints, 0, $nombreResultats);

            return [
                'success' => true,
                'points' => $checkoutPoints,
                'stats' => [
                    'total' => count($checkoutPoints),
                    'relay_points' => count(array_filter($checkoutPoints, fn($p) => $p['type'] === 'REL')),
                    'lockers' => count(array_filter($checkoutPoints, fn($p) => $p['type'] === 'LOC')),
                    'search_radius' => $rayonKm,
                    'postal_code' => $codePostal
                ]
            ];
        });
    }

    // --- Méthodes utilitaires existantes (les garder telles quelles) ---

    private function generateCPsAround($cp, $rayonKm = 10)
    {
        if (empty($cp) || strlen($cp) < 2) {
            return [$cp];
        }

        $cpList = [$cp];
        $departement = substr($cp, 0, 2);
        
        // Ajouter les départements limitrophes pour une recherche plus large
        $departementsLimitrophes = $this->getDepartementsLimitrophes($departement);
        
        // Générer des variations autour du CP principal
        $baseNumber = intval(substr($cp, 2));
        $variations = range(max(0, $baseNumber - 50), $baseNumber + 50);
        
        foreach ($variations as $variation) {
            $newCp = $departement . str_pad($variation, 3, '0', STR_PAD_LEFT);
            if (strlen($newCp) === 5) {
                $cpList[] = $newCp;
            }
        }
        
        // Ajouter quelques CP des départements voisins
        foreach ($departementsLimitrophes as $deptVoisin) {
            $cpList[] = $deptVoisin . '000';
            $cpList[] = $deptVoisin . '100';
        }

        return array_unique($cpList);
    }

    private function getDepartementsLimitrophes($dept)
    {
        $limitrophes = [
            '01' => ['38', '39', '69', '73', '74'],
            '02' => ['08', '51', '59', '60', '77', '80'],
            '03' => ['18', '23', '42', '58', '63', '71'],
            '75' => ['77', '78', '91', '92', '93', '94', '95'],
            '77' => ['02', '10', '51', '60', '75', '89', '91', '94', '95'],
            '78' => ['27', '28', '60', '75', '91', '92', '95'],
            // Ajouter d'autres selon les besoins
        ];

        return $limitrophes[$dept] ?? [];
    }

    private function generateCPsAroundCoordinates($latitude, $longitude, $rayonKm = 10)
    {
        // Approximation simple : 1 degré ≈ 111km
        $deltaLat = $rayonKm / 111;
        $deltaLon = $rayonKm / (111 * cos(deg2rad($latitude)));
        
        $cpList = [];
        
        // Générer une grille autour des coordonnées
        for ($lat = $latitude - $deltaLat; $lat <= $latitude + $deltaLat; $lat += $deltaLat/2) {
            for ($lon = $longitude - $deltaLon; $lon <= $longitude + $deltaLon; $lon += $deltaLon/2) {
                // Convertir les coordonnées en CP approximatif (très simplifié)
                // Dans un vrai système, il faudrait une API de géocodage inverse
                $estimatedDept = $this->coordinatesToDepartement($lat, $lon);
                if ($estimatedDept) {
                    $cpList[] = $estimatedDept . '000';
                    $cpList[] = $estimatedDept . '100';
                    $cpList[] = $estimatedDept . '200';
                }
            }
        }
        
        return array_unique($cpList);
    }

    private function coordinatesToDepartement($lat, $lon)
    {
        // Approximation grossière basée sur les coordonnées
        // En production, utiliser une vraie API de géocodage
        if ($lat >= 48.5 && $lat <= 49.2 && $lon >= 2.0 && $lon <= 2.8) {
            return '75'; // Paris
        }
        if ($lat >= 45.0 && $lat <= 46.0 && $lon >= 4.5 && $lon <= 5.5) {
            return '69'; // Lyon
        }
        
        return '75'; // Default Paris
    }

    private function generateSignatureWSI2($params)
    {
        $signature = $params['Enseigne'];
        $signature .= $params['Pays'] ?? '';
        $signature .= $params['CP'] ?? '';
        $signature .= $params['NbResult'] ?? '';
        $signature .= $params['TypeActivite'] ?? '';
        $signature .= $this->config['v1']['private_key'];
        
        return strtoupper(md5($signature));
    }

    public function trackPackage($expeditionNumber)
    {
        if (!$this->config['enabled']) {
            return $this->mockTracking($expeditionNumber);
        }

        try {
            $params = [
                'Enseigne' => $this->config['v1']['enseigne'],
                'Expedition' => $expeditionNumber,
                'Langue' => 'FR'
            ];

            $params['Security'] = $this->generateSignature($params);

            $response = $this->soapRequest('WSI2_TracingColisDetaille', $params);

            if ($response && isset($response->WSI2_TracingColisDetailleResult)) {
                return $this->parseTrackingResponse($response->WSI2_TracingColisDetailleResult);
            }

            throw new \Exception('Réponse API invalide');

        } catch (\Exception $e) {
            Log::error('Erreur Mondial Relay trackPackage: ' . $e->getMessage());
            return $this->mockTracking($expeditionNumber);
        }
    }

    private function soapRequest($method, $params)
    {
        try {
            $soapClient = $this->getSoapClient();
            
            Log::info("Mondial Relay SOAP Request", [
                'method' => $method,
                'params' => array_merge($params, ['Security' => '[HIDDEN]'])
            ]);

            $response = $soapClient->__soapCall($method, [$params]);

            Log::info("Mondial Relay SOAP Response", [
                'method' => $method,
                'response_type' => gettype($response),
                'has_result' => isset($response->{$method.'Result'})
            ]);

            return $response;

        } catch (\SoapFault $e) {
            Log::error("Erreur SOAP Mondial Relay", [
                'method' => $method,
                'error' => $e->getMessage(),
                'faultcode' => $e->faultcode ?? 'N/A',
                'faultstring' => $e->faultstring ?? 'N/A'
            ]);
            throw $e;
        } catch (\Exception $e) {
            Log::error("Erreur générale SOAP Mondial Relay", [
                'method' => $method,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    private function generateSignature($params)
    {
        $signature = '';
        
        // Ordre des champs pour WSI4_PointRelais_Recherche
        $fields = ['Enseigne', 'Pays', 'Ville', 'CP', 'Latitude', 'Longitude', 'Taille', 'Poids', 'Action', 'DelaiEnvoi', 'RayonRecherche', 'NombreResultats'];
        
        foreach ($fields as $field) {
            $signature .= $params[$field] ?? '';
        }
        
        $signature .= $this->config['v1']['private_key'];
        
        return strtoupper(md5($signature));
    }

    private function parseRelayPointsResponse($result, $type = 'REL')
    {
        try {
            if (!isset($result->STAT) || $result->STAT != '0') {
                return [
                    'success' => false,
                    'error' => 'Erreur API: ' . ($result->STAT ?? 'Statut inconnu')
                ];
            }

            $points = [];
            
            if (isset($result->PointsRelais->PointRelais_Details)) {
                $relayPoints = $result->PointsRelais->PointRelais_Details;
                
                if (!is_array($relayPoints)) {
                    $relayPoints = [$relayPoints];
                }

                foreach ($relayPoints as $point) {
                    $points[] = [
                        'id' => $point->Num,
                        'name' => $point->LgAdr1,
                        'address' => trim($point->LgAdr3 . ' ' . $point->CP . ' ' . $point->Ville),
                        'postal_code' => $point->CP,
                        'city' => $point->Ville,
                        'country' => $point->Pays,
                        'latitude' => $point->Latitude,
                        'longitude' => $point->Longitude,
                        'distance' => $point->Distance,
                        'phone' => $point->Tel ?? '',
                        'type' => $type,
                        'opening_hours' => $this->parseOpeningHours($point),
                        // Champs de compatibilité pour les commandes
                        'Num' => $point->Num,
                        'Nom' => $point->LgAdr1,
                        'Adresse' => trim($point->LgAdr3 . ' ' . $point->CP . ' ' . $point->Ville)
                    ];
                }
            }

            return [
                'success' => true,
                'points' => $points
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Erreur parsing: ' . $e->getMessage()
            ];
        }
    }

    private function parseOpeningHours($point)
    {
        $days = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
        $hours = [];
        
        for ($i = 1; $i <= 7; $i++) {
            $dayName = $days[$i - 1];
            $openAM = $point->{"Horaires_Ouverture_Lundi_" . $i} ?? '';
            $closeAM = $point->{"Horaires_Fermeture_Lundi_" . $i} ?? '';
            $openPM = $point->{"Horaires_Ouverture_Lundi_" . ($i + 7)} ?? '';
            $closePM = $point->{"Horaires_Fermeture_Lundi_" . ($i + 7)} ?? '';
            
            if ($openAM && $closeAM) {
                $dayHours = $openAM . '-' . $closeAM;
                if ($openPM && $closePM) {
                    $dayHours .= ', ' . $openPM . '-' . $closePM;
                }
                $hours[$dayName] = $dayHours;
            }
        }
        
        return $hours;
    }

    private function parseShippingLabelResponse($result)
    {
        try {
            if (!isset($result->STAT) || $result->STAT != '0') {
                return [
                    'success' => false,
                    'error' => 'Erreur création étiquette: ' . ($result->STAT ?? 'Statut inconnu')
                ];
            }

            return [
                'success' => true,
                'expedition_number' => $result->ExpeditionNum ?? '',
                'url_etiquette' => $result->URL_Etiquette ?? '',
                'tracking_url' => 'https://www.mondialrelay.fr/suivi-de-colis/?NumeroExpedition=' . ($result->ExpeditionNum ?? '')
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Erreur parsing étiquette: ' . $e->getMessage()
            ];
        }
    }

    private function parseTrackingResponse($result)
    {
        try {
            if (!isset($result->STAT) || $result->STAT != '0') {
                return [
                    'success' => false,
                    'error' => 'Erreur suivi: ' . ($result->STAT ?? 'Statut inconnu')
                ];
            }

            $events = [];
            
            if (isset($result->ret_WSI2_TracingColisDetaille)) {
                $tracking = $result->ret_WSI2_TracingColisDetaille;
                
                if (!is_array($tracking)) {
                    $tracking = [$tracking];
                }

                foreach ($tracking as $event) {
                    $events[] = [
                        'date' => $event->Date ?? '',
                        'hour' => $event->Heure ?? '',
                        'status' => $event->Libelle ?? '',
                        'location' => $event->Pays ?? ''
                    ];
                }
            }

            return [
                'success' => true,
                'events' => $events,
                'current_status' => !empty($events) ? $events[0]['status'] : 'Statut inconnu'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Erreur parsing suivi: ' . $e->getMessage()
            ];
        }
    }

    private function mockRelayPoints($params)
    {
        Log::info('Utilisation des données mockées Mondial Relay');
        
        $cp = $params['CP'] ?? '75001';
        $ville = $params['Ville'] ?? 'Paris';
        
        return [
            'success' => true,
            'points' => [
                [
                    'id' => 'REL001',
                    'name' => 'Tabac des Arts',
                    'address' => '123 Rue de Rivoli 75001 Paris',
                    'postal_code' => '75001',
                    'city' => 'Paris',
                    'country' => 'FR',
                    'latitude' => '48.8566',
                    'longitude' => '2.3522',
                    'distance' => '0.5',
                    'phone' => '01 42 60 30 30',
                    'type' => 'REL',
                    'opening_hours' => [
                        'Lundi' => '09:00-19:00',
                        'Mardi' => '09:00-19:00',
                        'Mercredi' => '09:00-19:00',
                        'Jeudi' => '09:00-19:00',
                        'Vendredi' => '09:00-19:00',
                        'Samedi' => '09:00-17:00',
                        'Dimanche' => 'Fermé'
                    ],
                    'Num' => 'REL001',
                    'Nom' => 'Tabac des Arts',
                    'Adresse' => '123 Rue de Rivoli 75001 Paris'
                ],
                [
                    'id' => 'LOC001',
                    'name' => 'Locker Châtelet',
                    'address' => '1 Place du Châtelet 75001 Paris',
                    'postal_code' => '75001',
                    'city' => 'Paris',
                    'country' => 'FR',
                    'latitude' => '48.8588',
                    'longitude' => '2.3469',
                    'distance' => '0.8',
                    'phone' => '',
                    'type' => 'LOC',
                    'opening_hours' => [
                        'Lundi' => '24h/24',
                        'Mardi' => '24h/24',
                        'Mercredi' => '24h/24',
                        'Jeudi' => '24h/24',
                        'Vendredi' => '24h/24',
                        'Samedi' => '24h/24',
                        'Dimanche' => '24h/24'
                    ],
                    'Num' => 'LOC001',
                    'Nom' => 'Locker Châtelet',
                    'Adresse' => '1 Place du Châtelet 75001 Paris'
                ],
                [
                    'id' => 'REL002',
                    'name' => 'Pressing du Louvre',
                    'address' => '45 Rue Saint-Honoré 75001 Paris',
                    'postal_code' => '75001',
                    'city' => 'Paris',
                    'country' => 'FR',
                    'latitude' => '48.8606',
                    'longitude' => '2.3376',
                    'distance' => '1.2',
                    'phone' => '01 42 33 44 55',
                    'type' => 'REL',
                    'opening_hours' => [
                        'Lundi' => '08:00-20:00',
                        'Mardi' => '08:00-20:00',
                        'Mercredi' => '08:00-20:00',
                        'Jeudi' => '08:00-20:00',
                        'Vendredi' => '08:00-20:00',
                        'Samedi' => '10:00-18:00',
                        'Dimanche' => 'Fermé'
                    ],
                    'Num' => 'REL002',
                    'Nom' => 'Pressing du Louvre',
                    'Adresse' => '45 Rue Saint-Honoré 75001 Paris'
                ]
            ],
            'stats' => [
                'total' => 3,
                'relay_points' => 2,
                'lockers' => 1
            ],
            'is_mock' => true
        ];
    }

    private function mockShippingLabel($params)
    {
        return [
            'success' => true,
            'expedition_number' => 'EXP' . time(),
            'url_etiquette' => 'https://exemple.com/etiquette.pdf',
            'tracking_url' => 'https://www.mondialrelay.fr/suivi-de-colis/',
            'is_mock' => true
        ];
    }

    private function mockTracking($expeditionNumber)
    {
        return [
            'success' => true,
            'events' => [
                [
                    'date' => date('Y-m-d'),
                    'hour' => date('H:i'),
                    'status' => 'Colis en cours de livraison',
                    'location' => 'Centre de tri Paris'
                ],
                [
                    'date' => date('Y-m-d', strtotime('-1 day')),
                    'hour' => '14:30',
                    'status' => 'Colis en transit',
                    'location' => 'Plateforme logistique'
                ],
                [
                    'date' => date('Y-m-d', strtotime('-2 days')),
                    'hour' => '09:15',
                    'status' => 'Colis pris en charge',
                    'location' => 'Point d\'enlèvement'
                ]
            ],
            'current_status' => 'Colis en cours de livraison',
            'is_mock' => true
        ];
    }

    public function testConnection()
    {
        try {
            Log::info('Test de connexion Mondial Relay');
            
            $testParams = [
                'CP' => '75001',
                'RayonRecherche' => '5',
                'NombreResultats' => '5'
            ];
            
            $result = $this->findRelayPoints($testParams, 'REL');
            
            return [
                'success' => $result['success'],
                'message' => $result['success'] ? 'Connexion réussie' : 'Connexion échouée',
                'points_found' => count($result['points'] ?? []),
                'is_mock' => $result['is_mock'] ?? false
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Erreur de connexion: ' . $e->getMessage()
            ];
        }
    }

    private function formatFullAddress($point)
    {
        return trim($point['address'] ?? $point['Adresse'] ?? '');
    }

    private function calculateDeliveryCost($type)
    {
        return $type === 'LOC' ? 3.90 : 4.90; // Prix indicatifs
    }
}
