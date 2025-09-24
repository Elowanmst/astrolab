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
                                    <p>Livraison en 3-5 jours ouvrés après fin des précommandes</p>
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
                                    <p>Livraison en 2-4 jours ouvrés après fin des précommandes</p>
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
                                <div class="checkout-form-group" style="display: flex; gap: 15px; align-items: flex-end;">
                                    <div style="flex: 0 0 140px;">
                                        <label for="relay-postal-code" class="checkout-label">Code postal</label>
                                        <input type="text" 
                                               id="relay-postal-code" 
                                               placeholder="Ex: 75001"
                                               class="checkout-input"
                                               style="width: 100%;">
                                    </div>
                                    <div style="flex: 1; min-width: 180px;">
                                        <label for="relay-city" class="checkout-label">Ville</label>
                                        <input type="text" 
                                               id="relay-city" 
                                               placeholder="Ex: Paris"
                                               class="checkout-input"
                                               style="width: 100%;">
                                    </div>
                                    <button type="button" id="search-relay-btn" class="btn-glass" style="
                                        white-space: nowrap;
                                        margin: 0 0 20px 0;
                                        flex-shrink: 0;
                                    ">
                                        Rechercher
                                    </button>
                                    </button>
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
                            <span>Sous-total:</span>
                            <span>{{ number_format($cart->getTotal(), 2) }}€</span>
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
/* Styles glass design pour le widget Mondial Relay */
.relay-search {
    margin-bottom: 20px;
}

.relay-point {
    background: rgba(255, 255, 255, 0.15);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 16px;
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease !important;
    margin-bottom: 10px;
}

.relay-point:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
    background: rgba(255, 255, 255, 0.25);
    border-color: rgba(255, 255, 255, 0.3);
}

.relay-point.selected {
    background: rgba(39, 174, 96, 0.25) !important;
    border-color: rgba(39, 174, 96, 0.4) !important;
    box-shadow: 0 8px 32px rgba(39, 174, 96, 0.2);
}

.select-relay-btn {
    background: rgba(52, 152, 219, 0.7) !important;
    border: 1px solid rgba(52, 152, 219, 0.3) !important;
    backdrop-filter: blur(10px) !important;
    -webkit-backdrop-filter: blur(10px) !important;
    border-radius: 12px !important;
    transition: all 0.3s ease !important;
}

.select-relay-btn:hover {
    background: rgba(41, 128, 185, 0.8) !important;
    border-color: rgba(41, 128, 185, 0.4) !important;
    transform: scale(1.05) !important;
    box-shadow: 0 8px 25px rgba(41, 128, 185, 0.3) !important;
}

#mondial-relay-widget .checkout-section {
    background: rgba(255, 255, 255, 0.15);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 20px;
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    padding: 24px;
    transition: all 0.3s ease;
}

#mondial-relay-widget .checkout-section:hover {
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
    background: rgba(255, 255, 255, 0.2);
}

#mondial-relay-widget .checkout-section-title {
    color: var(--astro-black);
    border-bottom: 2px solid rgba(255, 255, 255, 0.3);
    padding-bottom: 12px;
    margin-bottom: 20px;
    font-weight: 600;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

#relay-results {
    max-height: 400px;
    overflow-y: auto;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.15);
    border-radius: 16px;
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    padding: 16px;
    box-shadow: inset 0 2px 8px rgba(0, 0, 0, 0.1);
}

/* Styles spécifiques pour le bouton de recherche */
#search-relay-btn {
    background: rgba(255, 255, 255, 0.15) !important;
    border: 1px solid rgba(255, 255, 255, 0.2) !important;
    color: var(--astro-text-primary) !important;
}

#search-relay-btn:hover:not(:disabled) {
    background: rgba(255, 255, 255, 0.25) !important;
    border-color: rgba(255, 255, 255, 0.4) !important;
    color: var(--astro-text-primary) !important;
    transform: translateY(-2px) !important;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2) !important;
}

#search-relay-btn:disabled {
    opacity: 0.6 !important;
    cursor: not-allowed !important;
    transform: none !important;
}

/* Style glass pour les champs de saisie */
#relay-postal-code, #relay-city {
    background: rgba(255, 255, 255, 0.15) !important;
    border: 1px solid rgba(255, 255, 255, 0.2) !important;
    border-radius: 12px !important;
    backdrop-filter: blur(10px) !important;
    -webkit-backdrop-filter: blur(10px) !important;
    color: var(--astro-black) !important;
    padding: 12px 16px !important;
    transition: all 0.3s ease !important;
}

#relay-postal-code:focus, #relay-city:focus {
    background: rgba(255, 255, 255, 0.25) !important;
    border-color: rgba(52, 152, 219, 0.4) !important;
    box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1) !important;
    outline: none !important;
}

#relay-postal-code::placeholder, #relay-city::placeholder {
    color: rgba(0, 0, 0, 0.6) !important;
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
    
    const baseTotal = {{ $cart->getTotal() }};
    
    function updateTotal() {
        const shipping = homeShipping.checked ? 4.99 : 2.99;
        const total = baseTotal + shipping;
        
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
        
        // Afficher un message de recherche avec style glass
        relayList.innerHTML = `
            <div style="
                background: rgba(52, 152, 219, 0.15);
                border: 1px solid rgba(52, 152, 219, 0.2);
                border-radius: 16px;
                backdrop-filter: blur(10px);
                -webkit-backdrop-filter: blur(10px);
                box-shadow: 0 8px 32px rgba(52, 152, 219, 0.1);
                padding: 24px;
                text-align: center;
                color: #3498db;
                font-weight: 600;
                text-shadow: 0 1px 2px rgba(255,255,255,0.5);
                animation: pulse 1.5s ease-in-out infinite alternate;
            ">
                🔍 Recherche avec le package Mondial Relay...
            </div>
            <style>
                @keyframes pulse {
                    0% { opacity: 0.7; transform: scale(1); }
                    100% { opacity: 1; transform: scale(1.02); }
                }
            </style>
        `;
        relayResults.style.display = 'block';
        
        // Utiliser la ville du champ relay-city ou celle de livraison comme fallback
        const cityField = document.getElementById('shipping_city');
        let city = relayCity ? relayCity.value.trim() : '';
        if (!city && cityField) {
            city = cityField.value.trim();
        }
        
        // NOUVELLE API AVEC LE PACKAGE BMWSLY
        fetch('/checkout/delivery-points', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                postal_code: postalCode,
                city: city || '',
                limit: 30
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data && data.data.points.length > 0) {
                currentRelayPoints = data.data.points;
                displayRelayPoints(data.data.points);
                
                // Afficher les statistiques du package avec style glass
                if (data.data.stats) {
                    const stats = data.data.stats;
                    const statsHtml = `
                        <div style="
                            background: rgba(39, 174, 96, 0.15);
                            border: 1px solid rgba(39, 174, 96, 0.2);
                            border-radius: 16px;
                            backdrop-filter: blur(10px);
                            -webkit-backdrop-filter: blur(10px);
                            box-shadow: 0 8px 32px rgba(39, 174, 96, 0.1);
                            padding: 16px;
                            margin-bottom: 20px;
                            text-align: center;
                            font-size: 14px;
                            color: var(--astro-black);
                            font-weight: 600;
                            text-shadow: 0 1px 2px rgba(255,255,255,0.5);
                        ">
                            <strong style="color: #27ae60;">📦 Package Mondial Relay :</strong> 
                            ${stats.total} points trouvés 
                            (${stats.relay_points || 0} points relais, ${stats.lockers || 0} casiers)
                        </div>
                    `;
                    relayList.innerHTML = statsHtml + relayList.innerHTML;
                }
            } else {
                const message = data.message || 'Aucun point trouvé avec le package.';
                relayList.innerHTML = `
                    <div style="
                        background: rgba(231, 76, 60, 0.15);
                        border: 1px solid rgba(231, 76, 60, 0.2);
                        border-radius: 16px;
                        backdrop-filter: blur(10px);
                        -webkit-backdrop-filter: blur(10px);
                        box-shadow: 0 8px 32px rgba(231, 76, 60, 0.1);
                        padding: 20px;
                        text-align: center;
                        color: white;
                        font-weight: 600;
                    ">
                        ❌ ${message}
                    </div>
                `;
                relayResults.style.display = 'block';
                currentRelayPoints = [];
            }
        })
        .catch(error => {
            console.error('Erreur package:', error);
            relayList.innerHTML = `
                <div style="
                    background: rgba(231, 76, 60, 0.15);
                    border: 1px solid rgba(231, 76, 60, 0.2);
                    border-radius: 16px;
                    backdrop-filter: blur(10px);
                    -webkit-backdrop-filter: blur(10px);
                    box-shadow: 0 8px 32px rgba(231, 76, 60, 0.1);
                    padding: 20px;
                    text-align: center;
                    color: white;
                    font-weight: 600;
                ">
                    ⚠️ Erreur du package Mondial Relay. Veuillez réessayer.
                </div>
            `;
            relayResults.style.display = 'block';
            currentRelayPoints = [];
        })
        .finally(() => {
            searchRelayBtn.textContent = 'Rechercher';
            searchRelayBtn.disabled = false;
        });
    }
    
    // Afficher la liste des points relais avec style glass
    function displayRelayPoints(points) {
        let html = `
            <h5 style="
                margin-bottom: 20px;
                color: var(--astro-black);
                font-weight: 700;
                text-align: center;
                text-shadow: 0 2px 4px rgba(0,0,0,0.1);
                background: rgba(255,255,255,0.1);
                padding: 12px 20px;
                border-radius: 12px;
                backdrop-filter: blur(5px);
                -webkit-backdrop-filter: blur(5px);
                border: 1px solid rgba(255,255,255,0.15);
            ">
                📍 Points de collecte disponibles
            </h5>
        `;
        
        // Séparer les points relais et les lockers
        const relayPoints = points.filter(p => p.type === 'REL');
        const lockers = points.filter(p => p.type === 'LOC');
        
        // Afficher d'abord les lockers (souvent plus pratiques)
        if (lockers.length > 0) {
            html += `
                <h6 style="
                    margin: 20px 0 15px 0; 
                    color: #27ae60; 
                    font-weight: 700;
                    background: rgba(39, 174, 96, 0.1);
                    padding: 10px 16px;
                    border-radius: 12px;
                    backdrop-filter: blur(5px);
                    -webkit-backdrop-filter: blur(5px);
                    border: 1px solid rgba(39, 174, 96, 0.2);
                    text-shadow: 0 1px 2px rgba(255,255,255,0.5);
                    border-left: 4px solid #27ae60;
                ">
                    🔒 Casiers automatiques (24h/24)
                </h6>
            `;
            lockers.forEach(point => {
                html += generatePointHTML(point, '#27ae60', '🔒');
            });
        }
        
        // Puis les points relais classiques
        if (relayPoints.length > 0) {
            html += `
                <h6 style="
                    margin: 20px 0 15px 0; 
                    color: #3498db; 
                    font-weight: 700;
                    background: rgba(52, 152, 219, 0.1);
                    padding: 10px 16px;
                    border-radius: 12px;
                    backdrop-filter: blur(5px);
                    -webkit-backdrop-filter: blur(5px);
                    border: 1px solid rgba(52, 152, 219, 0.2);
                    text-shadow: 0 1px 2px rgba(255,255,255,0.5);
                    border-left: 4px solid #3498db;
                ">
                    🏪 Points relais
                </h6>
            `;
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
    
    // Générer le HTML pour un point de collecte avec style glass
    function generatePointHTML(point, color, icon) {
        const isLocker = point.type === 'LOC';
        const typeLabel = isLocker ? 'Casier automatique' : 'Point relais';
        const availabilityText = isLocker ? 'Accès 24h/24 - 7j/7' : 'Voir horaires sur place';
        
        // Convertir la couleur hex en rgba pour l'effet glass
        const glassColor = color === '#27ae60' ? 'rgba(39, 174, 96, 0.15)' : 'rgba(52, 152, 219, 0.15)';
        const borderColor = color === '#27ae60' ? 'rgba(39, 174, 96, 0.3)' : 'rgba(52, 152, 219, 0.3)';
        const accentColor = color === '#27ae60' ? 'rgba(39, 174, 96, 0.7)' : 'rgba(52, 152, 219, 0.7)';
        
        return `
            <div class="relay-point" data-point-id="${point.id}" style="
                background: rgba(255, 255, 255, 0.15);
                border: 1px solid rgba(255, 255, 255, 0.2);
                border-radius: 16px;
                backdrop-filter: blur(10px);
                -webkit-backdrop-filter: blur(10px);
                box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
                padding: 20px;
                margin-bottom: 12px;
                cursor: pointer;
                transition: all 0.3s ease;
                border-left: 4px solid ${color};
                position: relative;
                overflow: hidden;
            ">
                <!-- Effet de brillance subtil -->
                <div style="position: absolute; top: 0; left: 0; right: 0; height: 1px; background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);"></div>
                
                <div style="display: flex; justify-content: space-between; align-items: start;">
                    <div style="flex: 1;">
                        <div style="display: flex; align-items: center; margin-bottom: 8px;">
                            <span style="margin-right: 10px; font-size: 18px; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));">${icon}</span>
                            <h6 style="margin: 0; font-weight: 700; color: var(--astro-black); text-shadow: 0 1px 2px rgba(0,0,0,0.1); font-size: 16px;">${point.name}</h6>
                            <span style="
                                margin-left: 12px; 
                                background: ${accentColor}; 
                                color: white; 
                                padding: 4px 12px; 
                                border-radius: 20px; 
                                font-size: 11px; 
                                text-transform: uppercase; 
                                font-weight: 600;
                                backdrop-filter: blur(10px);
                                -webkit-backdrop-filter: blur(10px);
                                border: 1px solid rgba(255,255,255,0.2);
                                text-shadow: 0 1px 2px rgba(0,0,0,0.2);
                            ">
                                ${typeLabel}
                            </span>
                        </div>
                        <p style="margin: 0 0 6px 0; color: rgba(0,0,0,0.7); font-weight: 500; text-shadow: 0 1px 2px rgba(255,255,255,0.5);">${point.address}</p>
                        <p style="margin: 0 0 8px 0; color: rgba(0,0,0,0.7); font-weight: 500; text-shadow: 0 1px 2px rgba(255,255,255,0.5);">${point.postal_code} ${point.city}</p>
                        <div style="display: flex; justify-content: flex-end; align-items: center; margin-top: 10px;">
                            <small style="
                                color: #16a085; 
                                font-weight: 600;
                                background: rgba(22, 160, 133, 0.1);
                                padding: 4px 8px;
                                border-radius: 8px;
                                backdrop-filter: blur(5px);
                                -webkit-backdrop-filter: blur(5px);
                                border: 1px solid rgba(22, 160, 133, 0.2);
                            ">
                                ⏰ ${availabilityText}
                            </small>
                        </div>
                        ${point.phone ? `
                            <small style="
                                color: rgba(0,0,0,0.6); 
                                display: block; 
                                margin-top: 6px;
                                background: rgba(255,255,255,0.1);
                                padding: 3px 6px;
                                border-radius: 6px;
                                backdrop-filter: blur(5px);
                                -webkit-backdrop-filter: blur(5px);
                                border: 1px solid rgba(255,255,255,0.1);
                                width: fit-content;
                            ">📞 ${point.phone}</small>
                        ` : ''}
                    </div>
                    <button type="button" class="select-relay-btn" data-point='${JSON.stringify(point)}' style="
                        background: ${accentColor};
                        color: white;
                        border: 1px solid rgba(255,255,255,0.2);
                        padding: 12px 18px;
                        border-radius: 12px;
                        cursor: pointer;
                        font-size: 13px;
                        font-weight: 600;
                        transition: all 0.3s ease;
                        backdrop-filter: blur(10px);
                        -webkit-backdrop-filter: blur(10px);
                        text-shadow: 0 1px 2px rgba(0,0,0,0.2);
                        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
                        margin-left: 15px;
                    ">
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
