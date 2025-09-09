<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exemple d'intégration - Points de livraison Mondial Relay</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .checkout-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        input[type="text"], input[type="number"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        
        button {
            background: #007cba;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        
        button:hover {
            background: #005a8b;
        }
        
        button:disabled {
            background: #ccc;
            cursor: not-allowed;
        }
        
        .loading {
            text-align: center;
            padding: 20px;
            color: #666;
        }
        
        .error {
            color: #dc3545;
            background: #f8d7da;
            padding: 10px;
            border-radius: 4px;
            margin: 10px 0;
        }
        
        .success {
            color: #155724;
            background: #d4edda;
            padding: 10px;
            border-radius: 4px;
            margin: 10px 0;
        }
        
        .delivery-points-container {
            margin-top: 20px;
        }
        
        .stats {
            background: #e9ecef;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        
        .tabs {
            display: flex;
            margin-bottom: 15px;
            border-bottom: 1px solid #ddd;
        }
        
        .tab {
            padding: 10px 20px;
            cursor: pointer;
            border-bottom: 2px solid transparent;
        }
        
        .tab.active {
            border-bottom-color: #007cba;
            color: #007cba;
            font-weight: bold;
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
        }
        
        #selectedPointInfo {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            padding: 15px;
            border-radius: 4px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <h1>🚚 Exemple d'intégration Mondial Relay - Checkout</h1>
    
    <div class="checkout-section">
        <h2>1. Sélection de l'adresse de livraison</h2>
        
        <form id="searchForm">
            <div class="form-group">
                <label for="postalCode">Code postal *</label>
                <input type="text" id="postalCode" name="postal_code" placeholder="Ex: 75001" maxlength="5" required>
            </div>
            
            <div class="form-group">
                <label for="city">Ville (optionnel)</label>
                <input type="text" id="city" name="city" placeholder="Ex: Paris">
            </div>
            
            <div class="form-group">
                <label for="radius">Rayon de recherche (km)</label>
                <input type="number" id="radius" name="radius" value="15" min="5" max="50">
            </div>
            
            <button type="submit" id="searchBtn">
                🔍 Rechercher les points de livraison
            </button>
        </form>
    </div>
    
    <div id="loadingIndicator" class="loading" style="display: none;">
        <p>⏳ Recherche des points de livraison en cours...</p>
    </div>
    
    <div id="errorContainer"></div>
    
    <div id="resultsContainer" class="delivery-points-container" style="display: none;">
        <h2>2. Choix du point de livraison</h2>
        
        <div id="statsContainer" class="stats"></div>
        
        <div class="tabs">
            <div class="tab active" data-tab="all">Tous les points</div>
            <div class="tab" data-tab="relay_points">Points relais</div>
            <div class="tab" data-tab="lockers">Lockers automatiques</div>
        </div>
        
        <div id="tab-all" class="tab-content active"></div>
        <div id="tab-relay_points" class="tab-content"></div>
        <div id="tab-lockers" class="tab-content"></div>
    </div>
    
    <div id="selectedPointInfo" style="display: none;">
        <h3>✅ Point de livraison sélectionné</h3>
        <div id="selectedPointDetails"></div>
    </div>

    <!-- Inclure le service Mondial Relay -->
    <script src="/js/mondial-relay-checkout.js"></script>
    
    <script>
        // Initialisation du service
        const mondialRelay = initMondialRelayCheckout({
            onLoading: (isLoading) => {
                document.getElementById('loadingIndicator').style.display = isLoading ? 'block' : 'none';
                document.getElementById('searchBtn').disabled = isLoading;
            },
            onSuccess: (result) => {
                displayResults(result);
            },
            onError: (error) => {
                displayError('Erreur lors de la recherche: ' + error.message);
            }
        });

        // Gestion du formulaire de recherche
        document.getElementById('searchForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            clearMessages();
            
            const postalCode = document.getElementById('postalCode').value.trim();
            const city = document.getElementById('city').value.trim();
            const radius = parseInt(document.getElementById('radius').value);
            
            if (!/^\d{5}$/.test(postalCode)) {
                displayError('Le code postal doit contenir exactement 5 chiffres');
                return;
            }
            
            try {
                await mondialRelay.searchDeliveryPoints(postalCode, city, { radius });
            } catch (error) {
                // L'erreur est déjà gérée par le callback onError
            }
        });

        // Gestion des onglets
        document.querySelectorAll('.tab').forEach(tab => {
            tab.addEventListener('click', () => {
                // Retirer la classe active de tous les onglets
                document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
                document.querySelectorAll('.tab-content').forEach(tc => tc.classList.remove('active'));
                
                // Ajouter la classe active à l'onglet cliqué
                tab.classList.add('active');
                document.getElementById('tab-' + tab.dataset.tab).classList.add('active');
            });
        });

        // Écouter les sélections de points
        document.addEventListener('mondialRelay:pointSelected', (event) => {
            displaySelectedPoint(event.detail.point);
        });

        function displayResults(result) {
            const container = document.getElementById('resultsContainer');
            const statsContainer = document.getElementById('statsContainer');
            
            // Afficher les statistiques
            statsContainer.innerHTML = `
                <h3>📊 Résultats de la recherche</h3>
                <p><strong>${result.stats.total}</strong> point(s) de livraison trouvé(s)</p>
                <ul>
                    <li><strong>${result.stats.relay_points}</strong> point(s) relais classique(s)</li>
                    <li><strong>${result.stats.lockers}</strong> locker(s) automatique(s)</li>
                </ul>
                <p><em>Zone de recherche : ${result.stats.search_area} (rayon ${result.stats.search_radius} km)</em></p>
            `;
            
            // Afficher les points par catégorie
            displayPointsInTab('all', result.points.all);
            displayPointsInTab('relay_points', result.points.relay_points);
            displayPointsInTab('lockers', result.points.lockers);
            
            container.style.display = 'block';
            
            // Message de succès
            if (result.stats.total > 0) {
                displaySuccess(result.message);
            } else {
                displayError('Aucun point de livraison trouvé dans cette zone. Essayez d\'élargir le rayon de recherche.');
            }
        }

        function displayPointsInTab(tabName, points) {
            const tabContainer = document.getElementById('tab-' + tabName);
            
            if (!points || points.length === 0) {
                tabContainer.innerHTML = '<p>Aucun point dans cette catégorie.</p>';
                return;
            }
            
            tabContainer.innerHTML = points.map(point => mondialRelay.generatePointHTML(point)).join('');
        }

        function displaySelectedPoint(point) {
            const container = document.getElementById('selectedPointInfo');
            const detailsContainer = document.getElementById('selectedPointDetails');
            
            detailsContainer.innerHTML = `
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <h4>${point.name} (${point.type_label})</h4>
                        <p>${point.full_address}</p>
                        ${point.phone ? `<p>📞 ${point.phone}</p>` : ''}
                    </div>
                    <div style="text-align: right;">
                        <p><strong>${point.delivery_cost}€</strong></p>
                        <p>${point.delivery_time}</p>
                        <p>${point.distance} km</p>
                    </div>
                </div>
            `;
            
            container.style.display = 'block';
            
            // Mettre à jour les styles des points
            document.querySelectorAll('.delivery-point').forEach(el => {
                el.classList.remove('selected');
            });
            document.querySelector(`[data-point-id="${point.id}"]`)?.classList.add('selected');
        }

        function displayError(message) {
            const container = document.getElementById('errorContainer');
            container.innerHTML = `<div class="error">${message}</div>`;
        }

        function displaySuccess(message) {
            const container = document.getElementById('errorContainer');
            container.innerHTML = `<div class="success">${message}</div>`;
        }

        function clearMessages() {
            document.getElementById('errorContainer').innerHTML = '';
            document.getElementById('resultsContainer').style.display = 'none';
            document.getElementById('selectedPointInfo').style.display = 'none';
        }

        // Fonction globale pour sélectionner un point (appelée depuis les boutons)
        function selectPoint(pointId) {
            if (!mondialRelay) return;
            
            // Trouver le point dans les résultats
            for (const [cacheKey, result] of mondialRelay.cache) {
                const point = result.points.all.find(p => p.id === pointId);
                if (point) {
                    mondialRelay.selectDeliveryPoint(point);
                    break;
                }
            }
        }

        // Test automatique au chargement de la page
        window.addEventListener('load', () => {
            console.log('🚀 Service Mondial Relay initialisé et prêt pour le checkout');
            
            // Pré-remplir avec un exemple
            document.getElementById('postalCode').value = '75001';
            document.getElementById('city').value = 'Paris';
        });
    </script>
</body>
</html>
