<?php

namespace App\Http\Controllers;

use App\Services\MondialRelayService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class MondialRelayController extends Controller
{
    protected $mondialRelayService;

    public function __construct(MondialRelayService $mondialRelayService)
    {
        $this->mondialRelayService = $mondialRelayService;
    }

    /**
     * Rechercher des points relais et lockers
     */
    public function searchRelayPoints(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'postal_code' => 'required|string|min:5|max:5',
            'city' => 'nullable|string|min:1|max:100',
            'country' => 'sometimes|string|size:2',
            'limit' => 'sometimes|integer|min:1|max:100',
            'type' => 'sometimes|string|in:all,REL,LOC',
            'rayon' => 'sometimes|integer|min:1|max:50',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => 'Paramètres invalides',
                'details' => $validator->errors()
            ], 400);
        }

        try {
            $params = [
                'CP' => $request->postal_code,
                'Ville' => $request->city ?: '',
                'Pays' => $request->get('country', 'FR'),
                'NombreResultats' => (string) $request->get('limit', 50)
            ];

            // Ajouter les coordonnées si fournies
            if ($request->has('latitude') && $request->has('longitude')) {
                $params['Latitude'] = (string) $request->input('latitude');
                $params['Longitude'] = (string) $request->input('longitude');
            }

            $searchType = $request->get('type', 'all');
            $result = $this->mondialRelayService->findRelayPoints($params, $searchType);

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'error' => $result['error'] ?? 'Erreur lors de la recherche'
                ], 500);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'points' => $result['points'],
                    'total' => count($result['points']),
                    'stats' => $result['stats'] ?? null,
                    'message' => $result['message'] ?? null
                ],
                'search_params' => [
                    'postal_code' => $params['CP'],
                    'city' => $params['Ville'],
                    'type' => $searchType,
                    'rayon' => $request->get('rayon', 10),
                    'limit' => $params['NombreResultats']
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur serveur: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Rechercher uniquement des lockers automatiques
     */
    public function searchLockers(Request $request): JsonResponse
    {
        $request->merge(['type' => 'LOC']);
        return $this->searchRelayPoints($request);
    }

    /**
     * Rechercher uniquement des points relais classiques
     */
    public function searchRelayPointsOnly(Request $request): JsonResponse
    {
        $request->merge(['type' => 'REL']);
        return $this->searchRelayPoints($request);
    }

    /**
     * Tester la connexion à l'API Mondial Relay
     */
    public function testConnection(): JsonResponse
    {
        try {
            $result = $this->mondialRelayService->testConnection();

            return response()->json([
                'success' => $result['success'],
                'message' => $result['message'],
                'data' => [
                    'points_found' => $result['points_found'] ?? 0,
                    'stats' => $result['stats'] ?? null,
                    'error' => $result['error'] ?? null
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors du test: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtenir les détails d'un point relais spécifique
     */
    public function getPointDetails(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'point_id' => 'required|string',
            'postal_code' => 'required|string|min:5|max:5'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => 'Paramètres invalides',
                'details' => $validator->errors()
            ], 400);
        }

        try {
            // Rechercher tous les points dans le CP
            $params = [
                'CP' => $request->input('postal_code'),
                'NombreResultats' => '100'
            ];

            $result = $this->mondialRelayService->findRelayPoints($params, 'all');
            
            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'error' => 'Erreur lors de la recherche'
                ], 500);
            }

            $pointId = $request->input('point_id');
            $foundPoint = null;

            // Chercher le point spécifique
            foreach ($result['points'] as $point) {
                if (($point['id'] ?? $point['Num'] ?? '') === $pointId) {
                    $foundPoint = $point;
                    break;
                }
            }

            if (!$foundPoint) {
                return response()->json([
                    'success' => false,
                    'error' => 'Point relais non trouvé'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $foundPoint
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur serveur: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Widget de recherche de points relais
     */
    public function widget(Request $request)
    {
        $postalCode = $request->get('postal_code', '75001');
        $city = $request->get('city', 'Paris');
        
        return view('mondial-relay.widget', compact('postalCode', 'city'));
    }

    /**
     * Dashboard administrateur
     */
    public function dashboard()
    {
        return view('mondial-relay.dashboard');
    }
}
