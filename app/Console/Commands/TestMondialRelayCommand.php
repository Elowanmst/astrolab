<?php

namespace App\Console\Commands;

use App\Services\MondialRelayService;
use Illuminate\Console\Command;

class TestMondialRelayCommand extends Command
{
    protected $signature = 'mondial-relay:test 
                            {--cp=75001 : Code postal de recherche}
                            {--ville=Paris : Ville de recherche}
                            {--type=all : Type de points (REL, LOC, all)}
                            {--rayon=10 : Rayon de recherche en km}
                            {--limit=20 : Nombre maximum de résultats}';

    protected $description = 'Tester la recherche de points relais et lockers Mondial Relay';

    private $mondialRelayService;

    public function __construct(MondialRelayService $mondialRelayService)
    {
        parent::__construct();
        $this->mondialRelayService = $mondialRelayService;
    }

    public function handle()
    {
        $cp = $this->option('cp');
        $ville = $this->option('ville');
        $type = $this->option('type');
        $rayon = (int) $this->option('rayon');
        $limit = (int) $this->option('limit');

        $this->info("🔍 Recherche de points Mondial Relay");
        $this->info("📍 Code postal: {$cp}");
        $this->info("🏙️  Ville: {$ville}");
        $this->info("📦 Type: {$type}");
        $this->info("📏 Rayon: {$rayon} km");
        $this->line('');

        // Paramètres de recherche
        $params = [
            'CP' => $cp,
            'Ville' => $ville,
            'NombreResultats' => (string) $limit
        ];

        try {
            $this->info("⏳ Recherche en cours...");
            $result = $this->mondialRelayService->findRelayPoints($params, $type);

            if (!$result['success']) {
                $this->error("❌ Erreur lors de la recherche");
                $this->error($result['error'] ?? 'Erreur inconnue');
                return 1;
            }

            $points = $result['points'] ?? [];
            $totalPoints = count($points);

            $this->info("✅ Recherche terminée avec succès");
            $this->info("📊 {$totalPoints} point(s) trouvé(s)");

            if (isset($result['stats'])) {
                $stats = $result['stats'];
                $this->info("   - Points relais (REL): " . ($stats['relay_points'] ?? 0));
                $this->info("   - Lockers (LOC): " . ($stats['lockers'] ?? 0));
            }

            $this->line('');

            if (empty($points)) {
                $this->warn("⚠️  " . ($result['message'] ?? 'Aucun point trouvé'));
                return 0;
            }

            // Afficher les résultats sous forme de tableau
            $tableData = [];
            foreach ($points as $index => $point) {
                $tableData[] = [
                    'N°' => $index + 1,
                    'ID' => $point['id'] ?? $point['Num'] ?? 'N/A',
                    'Type' => $point['type'] ?? 'N/A',
                    'Nom' => $point['name'] ?? $point['Nom'] ?? 'N/A',
                    'Adresse' => $point['address'] ?? $point['Adresse'] ?? 'N/A',
                    'CP' => $point['postal_code'] ?? 'N/A',
                    'Ville' => $point['city'] ?? 'N/A',
                    'Distance' => ($point['distance'] ?? 'N/A') . ' km',
                ];
            }

            $this->table([
                'N°', 'ID', 'Type', 'Nom', 'Adresse', 'CP', 'Ville', 'Distance'
            ], $tableData);

            // Afficher le détail du premier point
            if (!empty($points)) {
                $firstPoint = $points[0];
                $this->line('');
                $this->info("📋 Détail du premier point:");
                $this->info("   ID: " . ($firstPoint['id'] ?? $firstPoint['Num'] ?? 'N/A'));
                $this->info("   Type: " . ($firstPoint['type'] ?? 'N/A'));
                $this->info("   Nom: " . ($firstPoint['name'] ?? $firstPoint['Nom'] ?? 'N/A'));
                $this->info("   Adresse: " . ($firstPoint['address'] ?? $firstPoint['Adresse'] ?? 'N/A'));
                
                if (isset($firstPoint['phone']) && !empty($firstPoint['phone'])) {
                    $this->info("   Téléphone: " . $firstPoint['phone']);
                }
                
                if (isset($firstPoint['latitude']) && isset($firstPoint['longitude'])) {
                    $this->info("   Coordonnées: " . $firstPoint['latitude'] . ", " . $firstPoint['longitude']);
                }

                // Afficher les horaires si disponibles
                if (isset($firstPoint['opening_hours']) && is_array($firstPoint['opening_hours'])) {
                    $this->line('');
                    $this->info("🕐 Horaires d'ouverture:");
                    foreach ($firstPoint['opening_hours'] as $day => $hours) {
                        if (!empty($hours['morning']) || !empty($hours['afternoon'])) {
                            $schedule = [];
                            if (!empty($hours['morning'])) {
                                $schedule[] = $hours['morning'];
                            }
                            if (!empty($hours['afternoon'])) {
                                $schedule[] = $hours['afternoon'];
                            }
                            $this->info("   {$day}: " . implode(' / ', $schedule));
                        }
                    }
                }
            }

            $this->line('');
            $this->info("✨ Test terminé avec succès!");

            return 0;

        } catch (\Exception $e) {
            $this->error("💥 Erreur lors du test: " . $e->getMessage());
            $this->error("📍 Trace: " . $e->getFile() . ':' . $e->getLine());
            return 1;
        }
    }
}
