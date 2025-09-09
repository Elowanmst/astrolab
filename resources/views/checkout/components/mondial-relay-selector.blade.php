{{-- Composant JavaScript pour intégration dans le checkout --}}
<div class="delivery-method-selection" x-data="mondialRelayCheckout()">
    <div class="shipping-methods">
        <h3>Mode de livraison</h3>
        
        <div class="method-option">
            <input type="radio" id="home_delivery" name="shipping_method" value="home" x-model="selectedMethod" @change="resetSelection()">
            <label for="home_delivery">
                <strong>Livraison à domicile</strong>
                <span class="cost">6.90€</span>
                <p>Livraison en 48-72h à votre domicile</p>
            </label>
        </div>

        <div class="method-option">
            <input type="radio" id="pickup_delivery" name="shipping_method" value="pickup" x-model="selectedMethod" @change="initRelaySearch()">
            <label for="pickup_delivery">
                <strong>Mondial Relay - Point relais</strong>
                <span class="cost">À partir de 3.90€</span>
                <p>Retrait dans un point relais ou locker automatique</p>
            </label>
        </div>
    </div>

    {{-- Section de recherche de points relais (affichée si pickup sélectionné) --}}
    <div x-show="selectedMethod === 'pickup'" x-transition class="relay-search-section">
        <h4>Sélectionner un point de collecte</h4>
        
        <div class="search-form">
            <div class="form-row">
                <div class="form-group">
                    <label for="relay_postal_code">Code postal *</label>
                    <input 
                        type="text" 
                        id="relay_postal_code" 
                        x-model="searchPostalCode" 
                        @input.debounce.500ms="searchPoints()"
                        placeholder="Ex: 75001"
                        maxlength="5"
                        required
                    >
                </div>
                <div class="form-group">
                    <label for="relay_city">Ville</label>
                    <input 
                        type="text" 
                        id="relay_city" 
                        x-model="searchCity" 
                        @input.debounce.500ms="searchPoints()"
                        placeholder="Ex: Paris"
                    >
                </div>
            </div>
            
            <button type="button" @click="searchPoints()" :disabled="loading || !searchPostalCode">
                <span x-show="!loading">🔍 Rechercher</span>
                <span x-show="loading">⏳ Recherche...</span>
            </button>
        </div>

        {{-- Résultats de la recherche --}}
        <div x-show="searchResults.points && searchResults.points.all.length > 0" class="search-results">
            <div class="results-stats" x-show="searchResults.stats">
                <p>
                    <strong x-text="searchResults.stats.total"></strong> point(s) trouvé(s) :
                    <span x-text="searchResults.stats.relay_points"></span> point(s) relais,
                    <span x-text="searchResults.stats.lockers"></span> locker(s)
                </p>
            </div>

            <div class="points-list">
                <template x-for="point in searchResults.points.all" :key="point.id">
                    <div 
                        class="delivery-point" 
                        :class="{ 'selected': selectedPoint && selectedPoint.id === point.id }"
                        @click="selectPoint(point)"
                    >
                        <div class="point-header">
                            <div class="point-info">
                                <h5 x-text="point.name"></h5>
                                <span class="point-type" x-text="point.type_label"></span>
                            </div>
                            <div class="point-meta">
                                <span class="distance" x-text="point.distance + ' km'"></span>
                                <strong class="cost" x-text="point.delivery_cost + '€'"></strong>
                            </div>
                        </div>
                        <div class="point-details">
                            <p class="address" x-text="point.full_address"></p>
                            <p class="delivery-time" x-text="'Livraison: ' + point.delivery_time"></p>
                            <div x-show="point.phone" class="phone">
                                📞 <span x-text="point.phone"></span>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        {{-- Point sélectionné --}}
        <div x-show="selectedPoint" class="selected-point-summary">
            <h4>✅ Point de collecte sélectionné</h4>
            <div class="selected-point-info">
                <strong x-text="selectedPoint?.name"></strong> (<span x-text="selectedPoint?.type_label"></span>)
                <p x-text="selectedPoint?.full_address"></p>
                <div class="cost-summary">
                    <span>Coût de livraison: <strong x-text="selectedPoint?.delivery_cost + '€'"></strong></span>
                    <span>Délai: <span x-text="selectedPoint?.delivery_time"></span></span>
                </div>
            </div>
        </div>

        {{-- Champ caché pour le formulaire --}}
        <input type="hidden" name="selected_relay_point" :value="selectedPoint ? JSON.stringify(selectedPoint) : ''">
    </div>

    {{-- Messages d'erreur --}}
    <div x-show="error" class="error-message" x-text="error"></div>
</div>

<script>
function mondialRelayCheckout() {
    return {
        selectedMethod: 'home',
        searchPostalCode: '',
        searchCity: '',
        loading: false,
        error: '',
        searchResults: {},
        selectedPoint: null,

        initRelaySearch() {
            // Auto-remplir avec l'adresse de livraison si disponible
            const shippingPostal = document.querySelector('input[name="shipping_postal_code"]')?.value;
            const shippingCity = document.querySelector('input[name="shipping_city"]')?.value;
            
            if (shippingPostal && shippingPostal.match(/^\d{5}$/)) {
                this.searchPostalCode = shippingPostal;
                this.searchCity = shippingCity || '';
                // Lancer automatiquement la recherche
                this.$nextTick(() => this.searchPoints());
            }
        },

        resetSelection() {
            this.selectedPoint = null;
            this.error = '';
        },

        async searchPoints() {
            if (!this.searchPostalCode || !this.searchPostalCode.match(/^\d{5}$/)) {
                this.error = 'Veuillez saisir un code postal valide (5 chiffres)';
                return;
            }

            this.loading = true;
            this.error = '';
            
            try {
                const response = await fetch('/checkout/delivery-points', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        postal_code: this.searchPostalCode,
                        city: this.searchCity
                    })
                });

                const result = await response.json();
                
                if (result.success) {
                    this.searchResults = result;
                    if (result.points.all.length === 0) {
                        this.error = 'Aucun point de collecte trouvé dans cette zone. Essayez d\'élargir votre recherche.';
                    }
                } else {
                    this.error = result.error || 'Erreur lors de la recherche';
                }
            } catch (error) {
                console.error('Erreur recherche points:', error);
                this.error = 'Erreur de connexion. Veuillez réessayer.';
            } finally {
                this.loading = false;
            }
        },

        selectPoint(point) {
            this.selectedPoint = point;
            this.error = '';
            
            // Déclencher la validation côté serveur
            this.validatePoint(point);
        },

        async validatePoint(point) {
            try {
                const response = await fetch('/checkout/validate-delivery-point', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        point_id: point.id,
                        postal_code: this.searchPostalCode
                    })
                });

                const result = await response.json();
                
                if (result.success) {
                    // Mettre à jour le récapitulatif de coût si nécessaire
                    this.updateCostSummary(result.cart_summary);
                } else {
                    this.error = result.error || 'Point de livraison invalide';
                    this.selectedPoint = null;
                }
            } catch (error) {
                console.error('Erreur validation point:', error);
                this.error = 'Erreur de validation. Veuillez réessayer.';
                this.selectedPoint = null;
            }
        },

        updateCostSummary(summary) {
            // Mettre à jour l'affichage des coûts dans la page
            const shippingCostElement = document.querySelector('.shipping-cost');
            const totalElement = document.querySelector('.total-cost');
            
            if (shippingCostElement) {
                shippingCostElement.textContent = summary.shipping_cost.toFixed(2) + '€';
            }
            
            if (totalElement) {
                totalElement.textContent = summary.total_ttc.toFixed(2) + '€';
            }
        }
    }
}
</script>

<style>
.delivery-method-selection .method-option {
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 10px;
    cursor: pointer;
    transition: border-color 0.3s;
}

.delivery-method-selection .method-option:hover {
    border-color: #007cba;
}

.delivery-method-selection .method-option input:checked + label {
    color: #007cba;
    font-weight: bold;
}

.relay-search-section {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    margin-top: 15px;
}

.search-form .form-row {
    display: flex;
    gap: 15px;
    margin-bottom: 15px;
}

.search-form .form-group {
    flex: 1;
}

.search-form input {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.delivery-point {
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 10px;
    cursor: pointer;
    transition: all 0.3s;
}

.delivery-point:hover {
    border-color: #007cba;
    box-shadow: 0 2px 8px rgba(0, 124, 186, 0.1);
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

.point-type {
    font-size: 12px;
    background: #e9ecef;
    padding: 2px 6px;
    border-radius: 4px;
}

.point-meta {
    text-align: right;
}

.point-meta .cost {
    color: #007cba;
    font-weight: bold;
}

.selected-point-summary {
    background: #d4edda;
    border: 1px solid #c3e6cb;
    padding: 15px;
    border-radius: 8px;
    margin-top: 15px;
}

.error-message {
    color: #dc3545;
    background: #f8d7da;
    padding: 10px;
    border-radius: 4px;
    margin-top: 10px;
}
</style>
