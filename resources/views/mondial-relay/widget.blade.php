<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sélection Point Relais - Mondial Relay</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>
<body class="bg-gray-50">
    <div class="container mx-auto p-6">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-2xl font-bold mb-6 text-gray-800">
                🏪 Choisir un Point Relais Mondial Relay
            </h2>

            <!-- Formulaire de recherche -->
            <div class="mb-6 p-4 bg-blue-50 rounded-lg">
                <h3 class="font-semibold mb-3 text-blue-800">Recherche de points relais</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Code postal</label>
                        <input type="text" id="postalCode" class="w-full border border-gray-300 rounded-md px-3 py-2" 
                               value="{{ $defaultParams['postal_code'] }}" placeholder="75001">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ville</label>
                        <input type="text" id="city" class="w-full border border-gray-300 rounded-md px-3 py-2" 
                               value="{{ $defaultParams['city'] }}" placeholder="Paris">
                    </div>
                    <div class="flex items-end">
                        <button onclick="searchRelayPoints()" 
                                class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition duration-200">
                            🔍 Rechercher
                        </button>
                    </div>
                </div>
            </div>

            <!-- Indicateur de chargement -->
            <div id="loading" class="hidden text-center py-8">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                <p class="mt-2 text-gray-600">Recherche en cours...</p>
            </div>

            <!-- Résultats -->
            <div id="results" class="space-y-4">
                <!-- Les points relais s'afficheront ici -->
            </div>

            <!-- Point relais sélectionné -->
            <div id="selectedRelay" class="hidden mt-6 p-4 bg-green-50 border-l-4 border-green-400">
                <h3 class="font-semibold text-green-800 mb-2">✅ Point relais sélectionné</h3>
                <div id="selectedRelayInfo"></div>
                <div class="mt-3">
                    <button onclick="confirmSelection()" 
                            class="bg-green-600 text-white px-6 py-2 rounded-md hover:bg-green-700 mr-3">
                        Confirmer la sélection
                    </button>
                    <button onclick="cancelSelection()" 
                            class="bg-gray-400 text-white px-4 py-2 rounded-md hover:bg-gray-500">
                        Annuler
                    </button>
                </div>
            </div>

            <!-- Erreurs -->
            <div id="error" class="hidden mt-4 p-4 bg-red-50 border-l-4 border-red-400">
                <p class="text-red-800" id="errorMessage"></p>
            </div>
        </div>
    </div>

    <script>
        let selectedRelay = null;

        // Recherche automatique au chargement
        document.addEventListener('DOMContentLoaded', function() {
            searchRelayPoints();
        });

        function searchRelayPoints() {
            const postalCode = document.getElementById('postalCode').value;
            const city = document.getElementById('city').value;

            if (!postalCode || !city) {
                showError('Veuillez saisir un code postal et une ville');
                return;
            }

            showLoading(true);
            hideError();

            axios.get('/api/mondial-relay/relay-points/search', {
                params: {
                    postal_code: postalCode,
                    city: city
                }
            })
            .then(response => {
                showLoading(false);
                if (response.data.success) {
                    displayRelayPoints(response.data.points);
                } else {
                    showError(response.data.error || 'Erreur lors de la recherche');
                }
            })
            .catch(error => {
                showLoading(false);
                showError('Erreur de connexion: ' + (error.response?.data?.error || error.message));
            });
        }

        function displayRelayPoints(points) {
            const resultsDiv = document.getElementById('results');
            
            if (points.length === 0) {
                resultsDiv.innerHTML = '<p class="text-gray-600 text-center py-8">Aucun point de collecte trouvé dans cette zone.</p>';
                return;
            }

            // Séparer les lockers et points relais
            const lockers = points.filter(p => p.type === 'LOC');
            const relayPoints = points.filter(p => p.type === 'REL');
            
            let html = '<h3 class="font-semibold mb-4 text-gray-800">📍 Points de collecte disponibles (' + points.length + ')</h3>';
            
            // Afficher d'abord les lockers
            if (lockers.length > 0) {
                html += '<h4 class="font-medium mb-3 text-green-700 border-b border-green-200 pb-2">🔒 Casiers automatiques (24h/24)</h4>';
                lockers.forEach((point, index) => {
                    html += generatePointHTML(point, index, 'green', '🔒');
                });
            }
            
            // Puis les points relais
            if (relayPoints.length > 0) {
                html += '<h4 class="font-medium mb-3 mt-6 text-blue-700 border-b border-blue-200 pb-2">🏪 Points relais</h4>';
                relayPoints.forEach((point, index) => {
                    html += generatePointHTML(point, index + lockers.length, 'blue', '🏪');
                });
            }

            resultsDiv.innerHTML = html;
        }

        function generatePointHTML(point, index, colorScheme, icon) {
            const isLocker = point.type === 'LOC';
            const typeLabel = isLocker ? 'Casier automatique' : 'Point relais';
            const colorClasses = colorScheme === 'green' 
                ? 'text-green-800 bg-green-100 hover:bg-green-700' 
                : 'text-blue-800 bg-blue-100 hover:bg-blue-700';
            
            return `
                <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 cursor-pointer relay-point mb-3" 
                     onclick="selectRelay('${point.id}', ${index})">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <div class="flex items-center mb-2">
                                <span class="text-lg mr-2">${icon}</span>
                                <h4 class="font-semibold text-lg ${colorClasses.split(' ')[0]}">${point.name}</h4>
                                <span class="ml-2 px-2 py-1 text-xs rounded-full ${colorClasses.split(' ')[1]} ${colorClasses.split(' ')[0]}">
                                    ${typeLabel}
                                </span>
                            </div>
                            <p class="text-gray-600">${point.address}</p>
                            <p class="text-gray-600">${point.postal_code} ${point.city}</p>
                            ${point.phone ? `<p class="text-sm text-gray-500">📞 ${point.phone}</p>` : ''}
                        </div>
                        <div class="text-right">
                            <span class="${colorClasses.split(' ')[1]} ${colorClasses.split(' ')[0]} px-2 py-1 rounded text-sm">
                                📍 ${point.distance} km
                            </span>
                        </div>
                    </div>
                    
                    <div class="mt-3 grid grid-cols-2 gap-4 text-xs">
                        <div>
                            <h5 class="font-medium text-gray-700">Disponibilité</h5>
                            <div class="text-gray-600">
                                ${isLocker ? '<span class="text-green-600 font-bold">🟢 24h/24 - 7j/7</span>' : formatOpeningHours(point.opening_hours)}
                            </div>
                        </div>
                        <div>
                            <button class="w-full bg-${colorScheme}-600 text-white px-3 py-1 rounded ${colorClasses.split(' ')[2]}">
                                Sélectionner ce point
                            </button>
                        </div>
                    </div>
                </div>
            `;
        }

        function formatOpeningHours(hours) {
            if (!hours) return 'Horaires non disponibles';
            
            const today = new Date().getDay();
            const days = ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
            const todayName = days[today];
            
            if (hours[todayName]) {
                const todayHours = hours[todayName];
                let todaySchedule = '';
                if (todayHours.morning) todaySchedule += todayHours.morning;
                if (todayHours.afternoon) todaySchedule += (todaySchedule ? ', ' : '') + todayHours.afternoon;
                
                return `<strong>Aujourd'hui:</strong> ${todaySchedule || 'Fermé'}`;
            }
            
            return 'Horaires disponibles';
        }

        function selectRelay(relayId, index) {
            // Réinitialiser les styles
            document.querySelectorAll('.relay-point').forEach(el => {
                el.classList.remove('bg-blue-50', 'border-blue-300');
                el.classList.add('border-gray-200');
            });

            // Mettre en évidence le point sélectionné
            const selectedElement = document.querySelectorAll('.relay-point')[index];
            selectedElement.classList.add('bg-blue-50', 'border-blue-300');
            selectedElement.classList.remove('border-gray-200');

            // Récupérer les informations du point relais via l'API
            axios.get('/api/mondial-relay/relay-points/search', {
                params: {
                    postal_code: document.getElementById('postalCode').value,
                    city: document.getElementById('city').value
                }
            })
            .then(response => {
                if (response.data.success) {
                    const relay = response.data.points.find(p => p.id === relayId);
                    if (relay) {
                        selectedRelay = relay;
                        showSelectedRelay(relay);
                    }
                }
            });
        }

        function showSelectedRelay(relay) {
            const selectedDiv = document.getElementById('selectedRelay');
            const infoDiv = document.getElementById('selectedRelayInfo');
            
            infoDiv.innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <strong>${relay.name}</strong><br>
                        ${relay.address}<br>
                        ${relay.postal_code} ${relay.city}
                        ${relay.phone ? `<br>📞 ${relay.phone}` : ''}
                    </div>
                    <div>
                        <span class="text-sm text-gray-600">Distance: ${relay.distance} km</span>
                    </div>
                </div>
            `;
            
            selectedDiv.classList.remove('hidden');
        }

        function confirmSelection() {
            if (!selectedRelay) return;

            // Si le widget est intégré dans une page parent
            if (window.parent && window.parent.mondialRelayCallback) {
                window.parent.mondialRelayCallback(selectedRelay);
            } else {
                // Redirection vers la page de checkout avec le point relais sélectionné
                const params = new URLSearchParams({
                    relay_id: selectedRelay.id,
                    relay_name: selectedRelay.name,
                    relay_address: selectedRelay.address + ', ' + selectedRelay.postal_code + ' ' + selectedRelay.city
                });
                
                window.location.href = '/checkout/shipping?' + params.toString();
            }
        }

        function cancelSelection() {
            selectedRelay = null;
            document.getElementById('selectedRelay').classList.add('hidden');
            
            // Réinitialiser les styles
            document.querySelectorAll('.relay-point').forEach(el => {
                el.classList.remove('bg-blue-50', 'border-blue-300');
                el.classList.add('border-gray-200');
            });
        }

        function showLoading(show) {
            document.getElementById('loading').classList.toggle('hidden', !show);
        }

        function showError(message) {
            document.getElementById('error').classList.remove('hidden');
            document.getElementById('errorMessage').textContent = message;
        }

        function hideError() {
            document.getElementById('error').classList.add('hidden');
        }
    </script>
</body>
</html>
