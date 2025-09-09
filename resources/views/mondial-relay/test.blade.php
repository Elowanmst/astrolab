<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Mondial Relay</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-3xl font-bold text-gray-800 mb-8">🚚 Test Mondial Relay API</h1>
            
            <!-- Formulaire de test -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-8" x-data="mondialRelayTest()">
                <h2 class="text-xl font-semibold mb-4">Rechercher des points relais et lockers</h2>
                
                <form @submit.prevent="searchPoints()" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Code postal *</label>
                            <input type="text" x-model="searchForm.postal_code" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="75001" required maxlength="5">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ville</label>
                            <input type="text" x-model="searchForm.city" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="Paris">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                            <select x-model="searchForm.type" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="all">Tous (REL + LOC)</option>
                                <option value="REL">Points relais uniquement</option>
                                <option value="LOC">Lockers uniquement</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Limite résultats</label>
                            <input type="number" x-model="searchForm.limit" min="1" max="100"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="20">
                        </div>
                    </div>
                    
                    <div class="flex gap-4 items-center">
                        <button type="submit" :disabled="loading"
                                class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center">
                            <span x-show="loading" class="animate-spin mr-2">⏳</span>
                            <span x-text="loading ? 'Recherche...' : 'Rechercher'"></span>
                        </button>
                        
                        <button type="button" @click="testConnection()" :disabled="loading"
                                class="bg-green-600 text-white px-6 py-2 rounded-md hover:bg-green-700 disabled:opacity-50">
                            Test connexion
                        </button>
                    </div>
                </form>
                
                <!-- Messages -->
                <div x-show="error" x-text="error" class="mt-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded"></div>
                <div x-show="message" x-text="message" class="mt-4 p-3 bg-green-100 border border-green-400 text-green-700 rounded"></div>
            </div>
            
            <!-- Résultats -->
            <div x-show="results.points && results.points.length > 0" class="bg-white rounded-lg shadow-md p-6" x-data="mondialRelayTest()">
                <h2 class="text-xl font-semibold mb-4">
                    📍 Résultats (<span x-text="results.total || 0"></span> points trouvés)
                </h2>
                
                <!-- Statistiques -->
                <div x-show="results.stats" class="mb-4 p-3 bg-blue-50 rounded-lg">
                    <div class="grid grid-cols-3 gap-4 text-center">
                        <div>
                            <div class="text-2xl font-bold text-blue-600" x-text="results.stats?.total || 0"></div>
                            <div class="text-sm text-gray-600">Total</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-green-600" x-text="results.stats?.relay_points || 0"></div>
                            <div class="text-sm text-gray-600">Points relais</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-purple-600" x-text="results.stats?.lockers || 0"></div>
                            <div class="text-sm text-gray-600">Lockers</div>
                        </div>
                    </div>
                </div>
                
                <!-- Liste des points -->
                <div class="space-y-4">
                    <template x-for="(point, index) in results.points" :key="point.id || point.Num || index">
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-2">
                                        <span :class="point.type === 'LOC' ? 'bg-purple-100 text-purple-800' : 'bg-green-100 text-green-800'" 
                                              class="text-xs font-medium px-2 py-1 rounded-full" x-text="point.type"></span>
                                        <span class="text-sm text-gray-500" x-text="'#' + (point.id || point.Num || 'N/A')"></span>
                                    </div>
                                    
                                    <h3 class="font-semibold text-gray-900 mb-1" x-text="point.name || point.Nom || 'Nom non disponible'"></h3>
                                    <p class="text-sm text-gray-600 mb-2" x-text="point.address || point.Adresse || 'Adresse non disponible'"></p>
                                    
                                    <div class="flex items-center gap-4 text-xs text-gray-500">
                                        <span x-show="point.postal_code" x-text="point.postal_code"></span>
                                        <span x-show="point.city" x-text="point.city"></span>
                                        <span x-show="point.distance" x-text="point.distance + ' km'"></span>
                                        <span x-show="point.phone" x-text="point.phone"></span>
                                    </div>
                                </div>
                                
                                <div class="text-right">
                                    <button @click="showDetails(point)" 
                                            class="text-blue-600 hover:text-blue-800 text-sm">
                                        Voir détails
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Horaires (si disponibles) -->
                            <div x-show="point.opening_hours && Object.keys(point.opening_hours).length > 0" 
                                 class="mt-3 pt-3 border-t border-gray-100">
                                <details class="text-xs">
                                    <summary class="cursor-pointer text-gray-600">Horaires d'ouverture</summary>
                                    <div class="mt-2 grid grid-cols-2 gap-1">
                                        <template x-for="(hours, day) in point.opening_hours">
                                            <div x-show="hours.morning || hours.afternoon">
                                                <span class="font-medium" x-text="day + ':'"></span>
                                                <span x-text="[hours.morning, hours.afternoon].filter(h => h).join(' / ')"></span>
                                            </div>
                                        </template>
                                    </div>
                                </details>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <script>
        function mondialRelayTest() {
            return {
                searchForm: {
                    postal_code: '75001',
                    city: 'Paris',
                    type: 'all',
                    limit: 20
                },
                loading: false,
                error: '',
                message: '',
                results: {
                    points: [],
                    total: 0,
                    stats: null
                },
                
                async searchPoints() {
                    this.loading = true;
                    this.error = '';
                    this.message = '';
                    
                    try {
                        const response = await fetch('/api/mondial-relay/search', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify(this.searchForm)
                        });
                        
                        const data = await response.json();
                        
                        if (data.success) {
                            this.results = data.data;
                            this.message = data.data.message || `${data.data.total} point(s) trouvé(s)`;
                        } else {
                            this.error = data.error || 'Erreur lors de la recherche';
                        }
                    } catch (err) {
                        this.error = 'Erreur de connexion: ' + err.message;
                    } finally {
                        this.loading = false;
                    }
                },
                
                async testConnection() {
                    this.loading = true;
                    this.error = '';
                    this.message = '';
                    
                    try {
                        const response = await fetch('/api/mondial-relay/test-connection', {
                            method: 'GET',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        });
                        
                        const data = await response.json();
                        
                        if (data.success) {
                            this.message = data.message + (data.data.points_found ? ` (${data.data.points_found} points trouvés)` : '');
                        } else {
                            this.error = data.error || 'Erreur de connexion';
                        }
                    } catch (err) {
                        this.error = 'Erreur de connexion: ' + err.message;
                    } finally {
                        this.loading = false;
                    }
                },
                
                showDetails(point) {
                    alert(JSON.stringify(point, null, 2));
                }
            }
        }
    </script>
</body>
</html>
