@extends('layouts.app')

@section('content')
<div class="checkout-container">
    <!-- En-tête checkout -->
    <div class="checkout-header">
        <h1 class="checkout-title">ASTROLAB</h1>
        <h2 class="checkout-subtitle">Informations de livraison</h2>
        <p class="checkout-step">| ÉTAPE 2/3 : ADRESSE ET MODE DE LIVRAISON |</p>
        
        <div class="progress-bar step-2"></div>
    </div>

    @if ($errors->any())
        <div class="checkout-errors">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('checkout.payment') }}" method="POST">
        @csrf
        
        <div class="checkout-grid">
            <!-- Formulaire d'adresse -->
            <div class="checkout-main">
                <div class="checkout-section">
                    <h3 class="checkout-section-title">Adresse de livraison</h3>
                    
                    <div class="checkout-form-group">
                        <div class="checkout-form-grid two-columns">
                            <div>
                                <label for="shipping_name" class="checkout-label">Nom complet *</label>
                                <input type="text" 
                                       name="shipping_name" 
                                       id="shipping_name" 
                                       value="{{ old('shipping_name', $user?->name) }}"
                                       required 
                                       class="checkout-input">
                            </div>

                            <div>
                                <label for="shipping_email" class="checkout-label">Email *</label>
                                <input type="email" 
                                       name="shipping_email" 
                                       id="shipping_email" 
                                       value="{{ old('shipping_email', $user?->email) }}"
                                       required 
                                       class="checkout-input">
                            </div>
                        </div>
                    </div>

                    <div class="checkout-form-group">
                        <div class="checkout-form-grid">
                            <div>
                                <label for="shipping_phone" class="checkout-label">Téléphone *</label>
                                <input type="tel" 
                                       name="shipping_phone" 
                                       id="shipping_phone" 
                                       value="{{ old('shipping_phone', $user?->phone) }}"
                                       required 
                                       class="checkout-input">
                            </div>
                        </div>
                    </div>

                    <div class="checkout-form-group">
                        <div class="checkout-form-grid full-width">
                            <div>
                                <label for="shipping_address" class="checkout-label">Adresse *</label>
                                <textarea name="shipping_address" 
                                          id="shipping_address" 
                                          required 
                                          class="checkout-textarea">{{ old('shipping_address', $user?->address) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="checkout-form-group">
                        <div class="checkout-form-grid two-columns">
                            <div>
                                <label for="shipping_city" class="checkout-label">Ville *</label>
                                <input type="text" 
                                       name="shipping_city" 
                                       id="shipping_city" 
                                       value="{{ old('shipping_city', $user?->city) }}"
                                       required 
                                       class="checkout-input">
                            </div>

                            <div>
                                <label for="shipping_postal_code" class="checkout-label">Code postal *</label>
                                <input type="text" 
                                       name="shipping_postal_code" 
                                       id="shipping_postal_code" 
                                       value="{{ old('shipping_postal_code', $user?->postal_code) }}"
                                       required 
                                       class="checkout-input">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Mode de livraison -->
                <div class="checkout-section">
                    <h4 class="checkout-section-title">Mode de livraison</h4>
                    
                    <div class="checkout-options">
                        <div class="checkout-option selected">
                            <input type="radio" 
                                   name="shipping_method" 
                                   id="shipping_home" 
                                   value="home" 
                                   checked>
                            <div class="checkout-option-content">
                                <div class="checkout-option-info">
                                    <h4>Livraison à domicile</h4>
                                    <p>Livraison en 3-5 jours ouvrés</p>
                                </div>
                                <div class="checkout-option-price">4.99€</div>
                            </div>
                        </div>

                        <div class="checkout-option">
                            <input type="radio" 
                                   name="shipping_method" 
                                   id="shipping_pickup" 
                                   value="pickup">
                            <div class="checkout-option-content">
                                <div class="checkout-option-info">
                                    <h4>Point relais & Casiers</h4>
                                    <p>Livraison en 2-4 jours ouvrés</p>
                                </div>
                                <div class="checkout-option-price">2.99€</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Widget de sélection de point relais -->
                    <div id="mondial-relay-widget" style="display: none; margin-top: 20px;">
                        <div class="checkout-section">
                            <h4 class="checkout-section-title">
                                <i class="fas fa-map-marker-alt"></i>
                                Choisissez votre point de collecte
                            </h4>
                            
                            <div class="relay-search">
                                <div class="checkout-form-group" style="display: flex; gap: 10px; align-items: end;">
                                    <div>
                                        <label for="relay-postal-code" class="checkout-label">Code postal</label>
                                        <input type="text" 
                                               id="relay-postal-code" 
                                               placeholder="Ex: 75001"
                                               class="checkout-input"
                                               style="max-width: 120px;">
                                    </div>
                                    <div>
                                        <label for="relay-city" class="checkout-label">Ville</label>
                                        <input type="text" 
                                               id="relay-city" 
                                               placeholder="Ex: Paris"
                                               class="checkout-input"
                                               style="max-width: 150px;">
                                    </div>
                                    <button type="button" id="search-relay-btn" class="btn btn-sm btn-primary">
                                        Rechercher
                                    </button>
                                </div>
                                
                                <!-- Information sur les types de points -->
                                <div style="margin-top: 10px; padding: 10px; background: #f8f9fa; border-radius: 5px; font-size: 12px; color: #6c757d;">
                                    <div style="display: flex; justify-content: space-around; text-align: center;">
                                        <div>
                                            <strong style="color: #27ae60;">🔒 Casiers automatiques</strong><br>
                                            Accès 24h/24 - 7j/7
                                        </div>
                                        <div>
                                            <strong style="color: #3498db;">🏪 Points relais</strong><br>
                                            Accueil personnalisé
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div id="relay-results" style="display: none;">
                                <div id="relay-list"></div>
                            </div>
                            
                            <!-- Champ caché pour stocker le point relais sélectionné -->
                            <input type="hidden" name="selected_relay_point" id="selected-relay-point">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Résumé de commande -->
            <div class="checkout-summary">
                <div class="checkout-section">
                    <h3 class="checkout-section-title">Résumé</h3>
                    
                    @foreach($cart->get() as $item)
                        <div class="checkout-item">
                            <div class="checkout-item-image">
                                @if($item['image'])
                                    <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}">
                                @else
                                    <div class="checkout-item-placeholder">IMG</div>
                                @endif
                            </div>
                            <div class="checkout-item-info">
                                <div class="checkout-item-name">{{ $item['name'] }}</div>
                                <div class="checkout-item-details">{{ $item['quantity'] }} x {{ $item['price'] }}€</div>
                            </div>
                        </div>
                    @endforeach
                    
                    <div class="checkout-totals">
                        <div class="checkout-total-line">
                            <span>Sous-total HT:</span>
                            <span>{{ number_format($cart->getTotalHT(), 2) }}€</span>
                        </div>
                        <div class="checkout-total-line">
                            <span>TVA (20%):</span>
                            <span>{{ number_format($cart->getTVA(), 2) }}€</span>
                        </div>
                        <div class="checkout-total-line">
                            <span>Sous-total TTC:</span>
                            <span>{{ number_format($cart->getTotalTTC(), 2) }}€</span>
                        </div>
                        <div class="checkout-total-line">
                            <span>Livraison:</span>
                            <span id="shipping-cost">4.99€</span>
                        </div>
                        <div class="checkout-total-line final">
                            <span>Total:</span>
                            <span id="final-total">{{ number_format($cart->getFinalTotal('home'), 2) }}€</span>
                        </div>
                    </div>

                    <button type="submit" class="checkout-btn">
                        <i class="fas fa-arrow-right"></i>
                        Continuer vers le paiement
                    </button>
                </div>
            </div>
        </div>
    </form>

    <!-- Navigation -->
    <div class="checkout-navigation">
        <a href="{{ route('checkout.index') }}" class="checkout-btn secondary">
            <i class="fas fa-arrow-left"></i>
            Retour
        </a>
    </div>
</div>

<style>
/* Styles pour le widget Mondial Relay */
.relay-search {
    margin-bottom: 20px;
}

.relay-point {
    transition: all 0.3s ease !important;
}

.relay-point:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.relay-point.selected {
    background-color: #e8f5e8 !important;
    border-color: #27ae60 !important;
}

.select-relay-btn:hover {
    background-color: #2980b9 !important;
}

#mondial-relay-widget .checkout-section {
    border: 1px solid #3498db;
    border-radius: 8px;
    padding: 20px;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}

#mondial-relay-widget .checkout-section-title {
    color: #2c3e50;
    border-bottom: 2px solid #3498db;
    padding-bottom: 10px;
    margin-bottom: 20px;
}

#relay-results {
    max-height: 400px;
    overflow-y: auto;
    border: 1px solid #ddd;
    border-radius: 5px;
    padding: 15px;
    background: white;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const homeShipping = document.getElementById('shipping_home');
    const pickupShipping = document.getElementById('shipping_pickup');
    const shippingCost = document.getElementById('shipping-cost');
    const finalTotal = document.getElementById('final-total');
    const options = document.querySelectorAll('.checkout-option');
    const mondialRelayWidget = document.getElementById('mondial-relay-widget');
    const searchRelayBtn = document.getElementById('search-relay-btn');
    const relayPostalCode = document.getElementById('relay-postal-code');
    const relayCity = document.getElementById('relay-city');
    const relayResults = document.getElementById('relay-results');
    const relayList = document.getElementById('relay-list');
    const selectedRelayPoint = document.getElementById('selected-relay-point');
    
    let currentRelayPoints = [];
    
    const baseTotalTTC = {{ $cart->getTotalTTC() }};
    
    function updateTotal() {
        const shipping = homeShipping.checked ? 4.99 : 2.99;
        const total = baseTotalTTC + shipping;
        
        shippingCost.textContent = shipping.toFixed(2) + '€';
        finalTotal.textContent = total.toFixed(2) + '€';
    }
    
    function updateSelection() {
        options.forEach(option => option.classList.remove('selected'));
        if (homeShipping.checked) {
            homeShipping.closest('.checkout-option').classList.add('selected');
            // Cacher le widget Mondial Relay
            mondialRelayWidget.style.display = 'none';
        } else {
            pickupShipping.closest('.checkout-option').classList.add('selected');
            // Afficher le widget Mondial Relay
            mondialRelayWidget.style.display = 'block';
        }
    }
    
    // Recherche de points relais
    function searchRelayPoints() {
        const postalCode = relayPostalCode.value.trim();
        const relayCity = document.getElementById('relay-city');
        
        if (!postalCode || postalCode.length !== 5) {
            alert('Veuillez saisir un code postal valide (5 chiffres)');
            return;
        }
        
        searchRelayBtn.textContent = 'Recherche...';
        searchRelayBtn.disabled = true;
        
        // Afficher un message de recherche
        relayList.innerHTML = '<p style="color: #3498db; text-align: center; padding: 20px;">🔍 Recherche de points relais et casiers automatiques...</p>';
        relayResults.style.display = 'block';
        
        // Utiliser la ville du champ relay-city ou celle de livraison comme fallback
        const cityField = document.getElementById('shipping_city');
        let city = relayCity ? relayCity.value.trim() : '';
        if (!city && cityField) {
            city = cityField.value.trim();
        }
        
        // Préparer les données pour l'API
        const requestData = {
            postal_code: postalCode,
            city: city || '',
            type: 'all',
            limit: 30
        };
        
        // Nouvelle API Mondial Relay
        fetch('/api/mondial-relay/search', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(requestData)
        })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data && data.data.points.length > 0) {
                    currentRelayPoints = data.data.points;
                    displayRelayPoints(data.data.points);
                    
                    // Statistiques
                    if (data.data.stats) {
                        const stats = data.data.stats;
                        const statsHtml = `
                            <div style="background: #e8f5e8; padding: 10px; border-radius: 5px; margin-bottom: 15px; text-align: center; font-size: 12px;">
                                <strong>📊 Résultats trouvés :</strong> 
                                ${stats.total} points au total 
                                (${stats.relay_points || 0} points relais, ${stats.lockers || 0} casiers)
                            </div>
                        `;
                        relayList.innerHTML = statsHtml + relayList.innerHTML;
                    }
                } else {
                    const message = data.data?.message || 'Aucun point de collecte trouvé pour ce code postal.';
                    relayList.innerHTML = `<p style="color: #e74c3c;">${message}</p>`;
                    relayResults.style.display = 'block';
                    currentRelayPoints = [];
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                relayList.innerHTML = '<p style="color: #e74c3c;">Erreur lors de la recherche. Veuillez réessayer.</p>';
                relayResults.style.display = 'block';
                currentRelayPoints = [];
            })
            .finally(() => {
                searchRelayBtn.textContent = 'Rechercher';
                searchRelayBtn.disabled = false;
            });
    }
    
    // Afficher la liste des points relais
    function displayRelayPoints(points) {
        let html = '<h5 style="margin-bottom: 15px;">Points de collecte disponibles :</h5>';
        
        // Séparer les points relais et les lockers
        const relayPoints = points.filter(p => p.type === 'REL');
        const lockers = points.filter(p => p.type === 'LOC');
        
        // Afficher d'abord les lockers (souvent plus pratiques)
        if (lockers.length > 0) {
            html += '<h6 style="margin: 15px 0 10px 0; color: #27ae60; font-weight: bold;">🔒 Casiers automatiques (24h/24)</h6>';
            lockers.forEach(point => {
                html += generatePointHTML(point, '#27ae60', '🔒');
            });
        }
        
        // Puis les points relais classiques
        if (relayPoints.length > 0) {
            html += '<h6 style="margin: 15px 0 10px 0; color: #3498db; font-weight: bold;">🏪 Points relais</h6>';
            relayPoints.forEach(point => {
                html += generatePointHTML(point, '#3498db', '🏪');
            });
        }
        
        relayList.innerHTML = html;
        relayResults.style.display = 'block';
        
        // Ajouter les événements de sélection
        document.querySelectorAll('.select-relay-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const pointData = JSON.parse(this.getAttribute('data-point'));
                selectRelayPoint(pointData);
            });
        });
    }
    
    // Générer le HTML pour un point de collecte
    function generatePointHTML(point, color, icon) {
        const isLocker = point.type === 'LOC';
        const typeLabel = isLocker ? 'Casier automatique' : 'Point relais';
        const availabilityText = isLocker ? 'Accès 24h/24 - 7j/7' : 'Voir horaires sur place';
        
        return `
            <div class="relay-point" data-point-id="${point.id}" style="
                border: 1px solid #ddd; 
                padding: 15px; 
                margin-bottom: 10px; 
                cursor: pointer;
                border-radius: 5px;
                transition: all 0.3s ease;
                border-left: 4px solid ${color};
            " onmouseover="this.style.backgroundColor='#f8f9fa'" onmouseout="this.style.backgroundColor='white'">
                <div style="display: flex; justify-content: space-between; align-items: start;">
                    <div style="flex: 1;">
                        <div style="display: flex; align-items: center; margin-bottom: 5px;">
                            <span style="margin-right: 8px; font-size: 16px;">${icon}</span>
                            <h6 style="margin: 0; font-weight: bold; color: #2c3e50;">${point.name}</h6>
                            <span style="margin-left: 10px; background: ${color}; color: white; padding: 2px 8px; border-radius: 12px; font-size: 10px; text-transform: uppercase;">
                                ${typeLabel}
                            </span>
                        </div>
                        <p style="margin: 0 0 5px 0; color: #7f8c8d;">${point.address}</p>
                        <p style="margin: 0 0 5px 0; color: #7f8c8d;">${point.postal_code} ${point.city}</p>
                        <div style="display: flex; justify-content: flex-end; align-items: center; margin-top: 8px;">
                            <small style="color: #16a085; font-weight: bold;">
                                ⏰ ${availabilityText}
                            </small>
                        </div>
                        ${point.phone ? `<small style="color: #7f8c8d;">📞 ${point.phone}</small>` : ''}
                    </div>
                    <button type="button" class="select-relay-btn" data-point='${JSON.stringify(point)}' style="
                        background: ${color};
                        color: white;
                        border: none;
                        padding: 10px 16px;
                        border-radius: 6px;
                        cursor: pointer;
                        font-size: 12px;
                        font-weight: bold;
                        transition: all 0.3s ease;
                    " onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                        Sélectionner
                    </button>
                </div>
            </div>
        `;
    }
    
    // Sélectionner un point relais
    function selectRelayPoint(point) {    // Sélectionner un point depuis la carte
    function selectRelayFromMap(pointId) {
        const point = currentRelayPoints.find(p => p.id === pointId);
        if (point) {
            selectRelayPoint(point);
        }
    }
    
    // Basculer l'affichage de la carte
    function toggleMap() {
        if (mapContainer.style.display === 'none') {
            mapContainer.style.display = 'block';
            toggleMapBtn.textContent = '📍 Masquer la carte';
            
            if (!relayMap) {
                initMap();
            }
            
            // Forcer le redimensionnement de la carte
            setTimeout(() => {
                relayMap.invalidateSize();
                if (currentRelayPoints.length > 0) {
                    showPointsOnMap(currentRelayPoints);
                }
            }, 100);
        } else {
            mapContainer.style.display = 'none';
            toggleMapBtn.textContent = '📍 Afficher la carte';
        }
    }
        selectedRelayPoint.value = JSON.stringify(point);
        
        // Mettre à jour l'affichage
        document.querySelectorAll('.relay-point').forEach(rp => {
            rp.style.backgroundColor = 'white';
            rp.style.borderColor = '#ddd';
        });
        
        const selectedDiv = document.querySelector(`[data-point-id="${point.id}"]`);
        if (selectedDiv) {
            selectedDiv.style.backgroundColor = '#e8f5e8';
            selectedDiv.style.borderColor = '#27ae60';
        }
        
        // Afficher la confirmation
        const typeLabel = point.type === 'LOC' ? 'Casier automatique' : 'Point relais';
        const availabilityText = point.type === 'LOC' ? 'Accès 24h/24 - 7j/7' : 'Voir horaires sur place';
        
        alert(`${typeLabel} sélectionné :\n${point.name}\n${point.address}\n${point.postal_code} ${point.city}\n\n${availabilityText}`);
    }
    
    // Événements
    homeShipping.addEventListener('change', function() {
        updateTotal();
        updateSelection();
    });
    
    pickupShipping.addEventListener('change', function() {
        updateTotal();
        updateSelection();
    });
    
    searchRelayBtn.addEventListener('click', searchRelayPoints);
    
    relayPostalCode.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            searchRelayPoints();
        }
    });
    
    // Permettre de cliquer sur l'option entière
    options.forEach(option => {
        option.addEventListener('click', function() {
            const radio = this.querySelector('input[type="radio"]');
            radio.checked = true;
            updateTotal();
            updateSelection();
        });
    });
    
    // Pré-remplir le code postal et la ville si l'adresse est déjà saisie
    const shippingPostalCode = document.getElementById('shipping_postal_code');
    const shippingCity = document.getElementById('shipping_city');
    if (shippingPostalCode && shippingPostalCode.value) {
        relayPostalCode.value = shippingPostalCode.value;
    }
    if (shippingCity && shippingCity.value && relayCity) {
        relayCity.value = shippingCity.value;
    }
});
</script>
</script>
@endsection
