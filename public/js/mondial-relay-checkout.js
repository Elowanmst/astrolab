/**
 * Service JavaScript pour récupérer les points relais et lockers Mondial Relay
 * Spécialement conçu pour l'intégration dans le checkout
 */
class MondialRelayCheckoutService {
    constructor(options = {}) {
        this.apiUrl = options.apiUrl || '/api/mondial-relay/checkout-delivery-points';
        this.loadingCallback = options.onLoading || (() => {});
        this.successCallback = options.onSuccess || (() => {});
        this.errorCallback = options.onError || (() => {});
        this.cache = new Map();
        this.selectedPoint = null;
    }

    /**
     * Rechercher les points de livraison pour le checkout
     */
    async searchDeliveryPoints(postalCode, city = '', options = {}) {
        const cacheKey = `${postalCode}-${city}-${options.radius || 15}`;
        
        // Vérifier le cache pour éviter les appels redondants
        if (this.cache.has(cacheKey)) {
            const cachedResult = this.cache.get(cacheKey);
            this.successCallback(cachedResult);
            return cachedResult;
        }

        this.loadingCallback(true);

        try {
            const params = {
                postal_code: postalCode,
                city: city,
                radius: options.radius || 15,
                limit: options.limit || 30
            };

            console.log('🔍 Recherche points Mondial Relay:', params);

            const response = await fetch(this.apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(params)
            });

            if (!response.ok) {
                throw new Error(`Erreur HTTP: ${response.status}`);
            }

            const result = await response.json();
            
            if (result.success) {
                // Mettre en cache le résultat
                this.cache.set(cacheKey, result);
                
                console.log('✅ Points trouvés:', result.stats);
                this.successCallback(result);
                return result;
            } else {
                throw new Error(result.error || 'Erreur inconnue');
            }

        } catch (error) {
            console.error('❌ Erreur recherche Mondial Relay:', error);
            this.errorCallback(error);
            throw error;
        } finally {
            this.loadingCallback(false);
        }
    }

    /**
     * Sélectionner un point de livraison
     */
    selectDeliveryPoint(point) {
        this.selectedPoint = point;
        console.log('📦 Point sélectionné:', point);
        
        // Déclencher un événement personnalisé pour notifier la sélection
        const event = new CustomEvent('mondialRelay:pointSelected', {
            detail: { point }
        });
        document.dispatchEvent(event);
        
        return point;
    }

    /**
     * Obtenir le point actuellement sélectionné
     */
    getSelectedPoint() {
        return this.selectedPoint;
    }

    /**
     * Formater les points pour affichage dans une liste
     */
    formatPointsForDisplay(points) {
        return points.map(point => ({
            ...point,
            displayName: `${point.name} - ${point.city}`,
            displayAddress: point.full_address,
            displayType: point.type_label,
            displayDistance: `${point.distance} km`,
            displayCost: `${point.delivery_cost}€`,
            displayTime: point.delivery_time
        }));
    }

    /**
     * Générer le HTML pour afficher un point
     */
    generatePointHTML(point, isSelected = false) {
        const selectedClass = isSelected ? 'selected' : '';
        const iconClass = point.type === 'LOC' ? 'locker-icon' : 'relay-icon';
        
        return `
            <div class="delivery-point ${selectedClass}" data-point-id="${point.id}">
                <div class="point-header">
                    <span class="point-icon ${iconClass}"></span>
                    <div class="point-info">
                        <h4 class="point-name">${point.name}</h4>
                        <span class="point-type">${point.type_label}</span>
                    </div>
                    <div class="point-meta">
                        <span class="point-distance">${point.distance} km</span>
                        <span class="point-cost">${point.delivery_cost}€</span>
                    </div>
                </div>
                <div class="point-details">
                    <p class="point-address">${point.full_address}</p>
                    ${point.phone ? `<p class="point-phone">📞 ${point.phone}</p>` : ''}
                    <p class="point-delivery-time">⏱️ Livraison: ${point.delivery_time}</p>
                    ${this.generateOpeningHoursHTML(point.opening_hours, point.is_24h)}
                </div>
                <button class="select-point-btn" onclick="selectPoint('${point.id}')">
                    Choisir ce point
                </button>
            </div>
        `;
    }

    /**
     * Générer le HTML pour les horaires d'ouverture
     */
    generateOpeningHoursHTML(openingHours, is24h) {
        if (is24h) {
            return '<p class="opening-hours"><strong>🕐 Ouvert 24h/24</strong></p>';
        }

        if (!openingHours || Object.keys(openingHours).length === 0) {
            return '<p class="opening-hours">Horaires non disponibles</p>';
        }

        let html = '<div class="opening-hours"><strong>🕐 Horaires:</strong><ul>';
        
        Object.entries(openingHours).forEach(([day, hours]) => {
            if (hours.morning || hours.afternoon) {
                let schedule = '';
                if (hours.morning && hours.morning !== '') {
                    schedule += hours.morning;
                }
                if (hours.afternoon && hours.afternoon !== '') {
                    if (schedule) schedule += ', ';
                    schedule += hours.afternoon;
                }
                if (schedule) {
                    html += `<li><span class="day">${day}:</span> ${schedule}</li>`;
                }
            }
        });
        
        html += '</ul></div>';
        return html;
    }

    /**
     * Vider le cache
     */
    clearCache() {
        this.cache.clear();
        console.log('🗑️ Cache Mondial Relay vidé');
    }
}

// Fonctions utilitaires globales pour faciliter l'utilisation
let mondialRelayService = null;

function initMondialRelayCheckout(options = {}) {
    mondialRelayService = new MondialRelayCheckoutService(options);
    return mondialRelayService;
}

function searchMondialRelayPoints(postalCode, city = '', options = {}) {
    if (!mondialRelayService) {
        console.error('Service Mondial Relay non initialisé. Appelez initMondialRelayCheckout() d\'abord.');
        return Promise.reject('Service non initialisé');
    }
    
    return mondialRelayService.searchDeliveryPoints(postalCode, city, options);
}

function selectPoint(pointId) {
    if (!mondialRelayService) {
        console.error('Service Mondial Relay non initialisé.');
        return;
    }
    
    const selectedPoint = mondialRelayService.getSelectedPoint();
    if (selectedPoint && selectedPoint.id === pointId) {
        return; // Point déjà sélectionné
    }
    
    // Trouver le point dans les résultats cachés
    for (const [cacheKey, result] of mondialRelayService.cache) {
        const point = result.points.all.find(p => p.id === pointId);
        if (point) {
            mondialRelayService.selectDeliveryPoint(point);
            break;
        }
    }
}

// Styles CSS pour l'affichage (à ajouter dans votre CSS)
const mondialRelayCSSStyles = `
.delivery-point {
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 10px;
    cursor: pointer;
    transition: border-color 0.3s;
}

.delivery-point:hover {
    border-color: #007cba;
}

.delivery-point.selected {
    border-color: #007cba;
    background-color: #f0f8ff;
}

.point-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.point-info h4 {
    margin: 0;
    font-size: 16px;
    color: #333;
}

.point-type {
    font-size: 12px;
    color: #666;
    background: #e9ecef;
    padding: 2px 6px;
    border-radius: 4px;
}

.point-meta {
    text-align: right;
    font-size: 14px;
}

.point-distance {
    color: #666;
    margin-right: 10px;
}

.point-cost {
    font-weight: bold;
    color: #007cba;
}

.select-point-btn {
    background: #007cba;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 4px;
    cursor: pointer;
    width: 100%;
    margin-top: 10px;
}

.select-point-btn:hover {
    background: #005a8b;
}

.opening-hours ul {
    list-style: none;
    padding-left: 0;
    margin: 5px 0;
}

.opening-hours li {
    font-size: 12px;
    color: #666;
}

.opening-hours .day {
    font-weight: bold;
    min-width: 80px;
    display: inline-block;
}

.relay-icon::before { content: "🏪"; }
.locker-icon::before { content: "📦"; }
`;

// Auto-ajouter les styles si dans un navigateur
if (typeof document !== 'undefined') {
    const style = document.createElement('style');
    style.textContent = mondialRelayCSSStyles;
    document.head.appendChild(style);
}
