<?php

namespace App\Http\Controllers;

use App\Services\MondialRelayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MondialRelayController extends Controller
{
    protected $mondialRelayService;

    public function __construct(MondialRelayService $mondialRelayService)
    {
        $this->mondialRelayService = $mondialRelayService;
    }

    /**
     * Rechercher des points relais
     */
    public function searchRelayPoints(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'postal_code' => 'required|string|min:5|max:5',
            'city' => 'required|string|min:2|max:100',
            'country' => 'sometimes|string|size:2',
            'limit' => 'sometimes|integer|min:1|max:20'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => 'Paramètres invalides',
                'details' => $validator->errors()
            ], 400);
        }

        $params = [
            'CP' => $request->postal_code,
            'Ville' => $request->city,
            'Pays' => $request->get('country', 'FR'),
            'NombreResultats' => $request->get('limit', 10)
        ];

        $result = $this->mondialRelayService->findRelayPoints($params);

        return response()->json($result);
    }

    /**
     * Créer une étiquette d'expédition
     */
    public function createShippingLabel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|integer|exists:orders,id',
            'relay_point_id' => 'sometimes|string',
            'sender' => 'required|array',
            'recipient' => 'required|array',
            'package' => 'required|array'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => 'Paramètres invalides',
                'details' => $validator->errors()
            ], 400);
        }

        $sender = $request->sender;
        $recipient = $request->recipient;
        $package = $request->package;

        $params = [
            'NDossier' => $request->order_id,
            'NClient' => $request->order_id,
            
            // Expéditeur
            'Expe_Ad1' => $sender['name'] ?? '',
            'Expe_Ad3' => $sender['address'] ?? '',
            'Expe_Ville' => $sender['city'] ?? '',
            'Expe_CP' => $sender['postal_code'] ?? '',
            'Expe_Pays' => $sender['country'] ?? 'FR',
            'Expe_Tel1' => $sender['phone'] ?? '',
            'Expe_Mail' => $sender['email'] ?? '',
            
            // Destinataire
            'Dest_Ad1' => $recipient['name'] ?? '',
            'Dest_Ad3' => $recipient['address'] ?? '',
            'Dest_Ville' => $recipient['city'] ?? '',
            'Dest_CP' => $recipient['postal_code'] ?? '',
            'Dest_Pays' => $recipient['country'] ?? 'FR',
            'Dest_Tel1' => $recipient['phone'] ?? '',
            'Dest_Mail' => $recipient['email'] ?? '',
            
            // Colis
            'Poids' => $package['weight'] ?? '1000',
            'NbColis' => $package['quantity'] ?? '1',
            'CRT_Valeur' => $package['value'] ?? '0',
            'CRT_Devise' => 'EUR',
            
            // Point relais si livraison en point relais
            'LIV_Rel' => $request->relay_point_id ?? '',
            'LIV_Rel_Pays' => 'FR'
        ];

        $result = $this->mondialRelayService->createShippingLabel($params);

        return response()->json($result);
    }

    /**
     * Suivre un colis
     */
    public function trackPackage(Request $request, $trackingNumber)
    {
        if (empty($trackingNumber)) {
            return response()->json([
                'success' => false,
                'error' => 'Numéro de suivi requis'
            ], 400);
        }

        $result = $this->mondialRelayService->trackPackage($trackingNumber);

        return response()->json($result);
    }

    /**
     * Test de connexion à l'API
     */
    public function testConnection()
    {
        $result = $this->mondialRelayService->testConnection();

        return response()->json($result);
    }

    /**
     * Widget de sélection de point relais (page)
     */
    public function widget(Request $request)
    {
        $defaultParams = [
            'postal_code' => $request->get('cp', '75001'),
            'city' => $request->get('ville', 'Paris'),
            'country' => $request->get('pays', 'FR')
        ];

        return view('mondial-relay.widget', compact('defaultParams'));
    }

    /**
     * Affichage des points relais pour l'administration
     */
    public function dashboard()
    {
        return view('dashboard.mondial-relay.index');
    }

    /**
     * Gestion des étiquettes (admin)
     */
    public function labels()
    {
        return view('dashboard.mondial-relay.labels');
    }

    /**
     * Suivi des colis (admin)
     */
    public function tracking()
    {
        return view('dashboard.mondial-relay.tracking');
    }
}
