# Service Mondial Relay Laravel

## 📋 Description

Service PHP/Laravel complet pour récupérer les points relais et lockers automatiques Mondial Relay via les vraies API SOAP.

## ⚙️ Fonctionnalités

- ✅ **Recherche de points relais classiques** via `WSI4_PointRelais_Recherche` (REL)
- ✅ **Recherche de lockers automatiques** via `WSI2_PointRelais_Recherche` avec `TypeActivite = 24R` (LOC)
- ✅ **Recherche combinée** REL + LOC avec suppression des doublons
- ✅ **Recherche par code postal** ou ville
- ✅ **Rayon configurable** autour du CP/ville
- ✅ **Génération automatique des CP** dans un rayon donné
- ✅ **Signatures de sécurité** correctes pour les API Mondial Relay
- ✅ **Parsing robuste** des réponses SOAP
- ✅ **Fallback sur données mockées** en cas d'erreur API
- ✅ **Interface de test** web et ligne de commande

## 🚀 Installation et configuration

### 1. Variables d'environnement

Ajoutez dans votre `.env` :

```env
MONDIAL_RELAY_ENABLED=true
MONDIAL_RELAY_API_URL=https://api.mondialrelay.com
MONDIAL_RELAY_ENSEIGNE=CC235KWE
MONDIAL_RELAY_PRIVATE_KEY=1GixuOdd
MONDIAL_RELAY_MODE=production
```

### 2. Utilisation du service

```php
use App\Services\MondialRelayService;

// Injection dans un contrôleur
public function __construct(MondialRelayService $mondialRelayService)
{
    $this->mondialRelayService = $mondialRelayService;
}

// Recherche de tous les points (REL + LOC)
$result = $this->mondialRelayService->findRelayPoints([
    'CP' => '75001',
    'Ville' => 'Paris',
    'NombreResultats' => '50'
], 'all');

// Recherche de lockers uniquement
$result = $this->mondialRelayService->findRelayPoints([
    'CP' => '44130',
    'Ville' => 'Blain'
], 'LOC');

// Recherche de points relais uniquement
$result = $this->mondialRelayService->findRelayPoints([
    'CP' => '69000',
    'Ville' => 'Lyon'
], 'REL');
```

### 3. Structure de réponse

```php
[
    'success' => true,
    'points' => [
        [
            'id' => '024095',
            'name' => 'TABAC DE LA MAIRIE',
            'address' => '2 PLACE DE LA MAIRIE 75001 PARIS',
            'postal_code' => '75001',
            'city' => 'PARIS',
            'country' => 'FR',
            'latitude' => '48.8566',
            'longitude' => '2.3522',
            'distance' => '0.5',
            'phone' => '0142361234',
            'type' => 'REL', // ou 'LOC'
            'opening_hours' => [...],
            // Champs spécifiques Mondial Relay
            'Num' => '024095',
            'Nom' => 'TABAC DE LA MAIRIE',
            'Adresse' => '2 PLACE DE LA MAIRIE 75001 PARIS'
        ]
    ],
    'stats' => [
        'total' => 15,
        'relay_points' => 10,
        'lockers' => 5
    ]
]
```

## 🧪 Tests

### Commande Artisan

```bash
# Test basique
php artisan mondial-relay:test

# Test avec paramètres personnalisés
php artisan mondial-relay:test --cp=44130 --ville=Blain --type=LOC --rayon=20 --limit=30

# Test de lockers uniquement
php artisan mondial-relay:test --cp=75001 --type=LOC
```

### Interface web

Visitez : `/mondial-relay/test`

Interface interactive pour :
- Rechercher par code postal/ville
- Filtrer par type (REL/LOC/all)
- Voir les statistiques
- Afficher les détails des points
- Tester la connexion API

### API REST

```bash
# Recherche de points
curl -X POST http://localhost/api/mondial-relay/search \
  -H "Content-Type: application/json" \
  -d '{
    "postal_code": "75001",
    "city": "Paris",
    "type": "all",
    "limit": 20
  }'

# Test de connexion
curl http://localhost/api/mondial-relay/test-connection

# Recherche de lockers uniquement
curl -X POST http://localhost/api/mondial-relay/lockers \
  -H "Content-Type: application/json" \
  -d '{
    "postal_code": "44130",
    "city": "Blain"
  }'
```

## 📡 API Endpoints

### POST `/api/mondial-relay/search`
Recherche générale de points relais et lockers

**Paramètres :**
- `postal_code` (requis) : Code postal (5 chiffres)
- `city` (optionnel) : Nom de la ville
- `type` (optionnel) : `all`, `REL`, `LOC` (défaut: `all`)
- `limit` (optionnel) : Nombre max de résultats (défaut: 50)
- `rayon` (optionnel) : Rayon de recherche en km (défaut: 10)
- `latitude`, `longitude` (optionnel) : Coordonnées GPS

### POST `/api/mondial-relay/lockers`
Recherche de lockers automatiques uniquement

### POST `/api/mondial-relay/relay-points`
Recherche de points relais classiques uniquement

### GET `/api/mondial-relay/test-connection`
Test de la connexion à l'API Mondial Relay

## 🔧 Architecture technique

### Méthodes principales

#### `findRelayPoints($params, $type)`
- **$params** : Paramètres de recherche (CP, Ville, etc.)
- **$type** : `'all'`, `'REL'`, `'LOC'`
- **Retour** : Array avec success, points, stats

#### `searchByType($action, $params, $rayonKm)`
- Méthode interne pour appeler les API SOAP
- Gère REL (WSI4) et LOC (WSI2) séparément
- Fusion et déduplication des résultats

#### `generateCPsAround($cp, $rayonKm)`
- Génère les codes postaux dans un rayon donné
- Optimisé selon la taille du rayon
- Fallback intelligent sur départements

#### `generateSignatureWSI2($params)`
- Génération correcte de la signature Security pour WSI2
- Ordre spécifique : Enseigne + Pays + CP + NbResult + clé privée

### APIs SOAP utilisées

1. **WSI4_PointRelais_Recherche** : Points relais classiques
   - Action : `REL`
   - Signature basée sur tous les paramètres triés

2. **WSI2_PointRelais_Recherche** : Lockers automatiques
   - TypeActivite : `24R`
   - Signature spécifique WSI2

## 🎯 Cas d'usage

1. **Sélection de point de livraison** : Affichage d'une carte avec tous les points disponibles
2. **Création d'étiquettes** : Utilisation des `Num` retournés pour générer les étiquettes
3. **Calcul de distance** : Tri automatique par distance croissante
4. **Interface mobile** : API REST compatible avec applications mobiles

## 🔍 Débogage

Les logs sont automatiquement créés dans `storage/logs/laravel.log` :

```php
// Voir les requêtes SOAP
Log::info('Tentative de connexion SOAP vers Mondial Relay');

// Voir les résultats par type
Log::info('Points REL trouvés', ['count' => $count]);
Log::info('Lockers LOC trouvés', ['count' => $count]);

// Voir les erreurs API
Log::error('Erreur SOAP Mondial Relay', ['error' => $message]);
```

## 🚨 Gestion d'erreurs

- **Fallback automatique** sur données mockées en cas d'erreur API
- **Validation des paramètres** côté contrôleur
- **Gestion des timeouts** SOAP (30s)
- **Logs détaillés** pour le débogage

## 📚 Exemples concrets

### Exemple 1 : E-commerce avec sélection de point relais

```php
public function getDeliveryOptions(Request $request)
{
    $result = $this->mondialRelayService->findRelayPoints([
        'CP' => $request->postal_code,
        'Ville' => $request->city,
        'NombreResultats' => '20'
    ], 'all');
    
    return response()->json([
        'delivery_points' => $result['points'],
        'stats' => $result['stats']
    ]);
}
```

### Exemple 2 : Calcul du coût de livraison

```php
public function calculateShippingCost($pointId, $weight)
{
    // Récupérer les détails du point
    $result = $this->mondialRelayService->findRelayPoints(['CP' => '75001'], 'all');
    
    $selectedPoint = collect($result['points'])
        ->firstWhere('id', $pointId);
    
    if ($selectedPoint) {
        $cost = $selectedPoint['type'] === 'LOC' ? 4.90 : 3.90; // Exemple
        return $cost;
    }
    
    return null;
}
```

## 📄 Licence

Ce service est fourni dans le cadre du projet Astrolab.

---

**💡 Astuce** : Pour une utilisation en production, pensez à mettre en cache les résultats pour éviter trop d'appels API.
