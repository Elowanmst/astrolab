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
                        @foreach($cart->getAvailableShippingMethods() as $method)
                        <div class="checkout-option {{ $loop->first ? 'selected' : '' }}">
                            <input type="radio"
                            name="shipping_method"
                            id="shipping_{{ $method->code }}"
                            value="{{ $method->code }}"
                            {{ $loop->first ? 'checked' : '' }}>
                            <div class="checkout-option-content">
                                <div class="checkout-option-info">
                                    <h4>{{ $method->name }}</h4>
                                    <p>Livraison en {{ $method->getEstimatedDeliveryAttribute() ?? 'x jours' }}</p>
                                </div>
                                <div class="checkout-option-price">
                                    {{ number_format($method->calculatePrice($cart->getTotalHT()), 2) }}€
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    
                    
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
                                    <p>Livraison en 3 semaines après fin des précommandes</p>
                                </div>
                                <div class="checkout-option-price">6.99€</div>
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
                                    <p>Livraison en 2-3 semaines après fin des précommandes</p>
                                </div>
                                <div class="checkout-option-price">4.99€</div>
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
                        <span id="shipping-cost">6.99€</span>
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

        box-shadow: 0 8px 32px rgba(39, 174, 96, 0.2);
    }
    
    .select-relay-btn {
        background: rgba(78, 78, 78, 0.7) !important;
        border: 1px solid rgba(255, 255, 255, 0.3) !important;
        border-radius: 12px !important;
        transition: all 0.3s ease !important;
    }
    
    .select-relay-btn:hover {
        border-color: rgba(32, 32, 32, 0.4) !important;
        transform: scargba(255, 255, 255, 0.4)
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
    
    /* Animations pour les notifications et sélection */
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    
    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
    
    .relay-point.selected {
        border-color: rgba(12, 255, 49, 1) !important;
        box-shadow: 0 8px 32px rgba(39, 174, 96, 0.2) !important;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('🚀 Script points relais chargé');
        
        // Éléments DOM
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
        
        // ✅ FONCTION SÉLECTION SIMPLIFIÉE
        window.selectRelayPoint = function(point) {
            console.log('📍 Sélection point relais:', point);
            
            if (!selectedRelayPoint) {
                console.error('❌ Champ selected-relay-point manquant');
                alert('Erreur technique: champ de sélection manquant');
                return;
            }
            
            try {
                // Stocker le point sélectionné
                selectedRelayPoint.value = JSON.stringify(point);
                console.log('💾 Point stocké dans input hidden');
                
                // Effet visuel - réinitialiser tous les points
                document.querySelectorAll('.relay-point').forEach(rp => {
                    rp.classList.remove('selected');
                    rp.style.background = 'rgba(255, 255, 255, 0.15)';
                    rp.style.borderColor = 'rgba(255, 255, 255, 0.2)';
                    rp.style.boxShadow = 'none';
                });
                
                // Sélectionner visuellement le point choisi
                const selectedDiv = document.querySelector(`[data-point-id="${point.id}"]`);
                if (selectedDiv) {
                    selectedDiv.classList.add('selected');
                    selectedDiv.style.background = 'rgba(39, 174, 96, 0.25)';
                    selectedDiv.style.borderColor = 'rgba(39, 174, 96, 0.4)';
                    selectedDiv.style.boxShadow = '0 8px 32px rgba(39, 174, 96, 0.2)';
                    console.log('🎨 Effet visuel appliqué');
                }
                
                // Notification de succès
                const typeLabel = point.type === 'LOC' ? 'Casier automatique' : 'Point relais';
                showNotification(`✅ ${typeLabel} sélectionné !`, `${point.name}\\n${point.address}`, 'success');
                
                console.log('✅ Sélection réussie pour point:', point.id);
                
            } catch (error) {
                console.error('❌ Erreur lors de la sélection:', error);
                alert('Erreur lors de la sélection du point relais: ' + error.message);
            }
        };
        
        // ✅ FONCTION NOTIFICATION SIMPLIFIÉE
        function showNotification(title, message, type = 'success') {
            const bgColor = type === 'success' ? '#27ae60' : '#e74c3c';
            
            const notification = document.createElement('div');
            notification.innerHTML = `
            <div style="font-size: 16px; font-weight: bold; margin-bottom: 4px;">${title}</div>
            <div style="font-size: 14px; opacity: 0.9;">${message}</div>
        `;
            notification.style.cssText = `
            position: fixed; top: 20px; right: 20px; z-index: 9999;
            background: ${bgColor}; color: white; padding: 16px 20px;
            border-radius: 12px; font-family: inherit; max-width: 300px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.3); backdrop-filter: blur(10px);
            animation: slideIn 0.3s ease;
        `;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.style.animation = 'slideOut 0.3s ease forwards';
                setTimeout(() => {
                    if (document.body.contains(notification)) {
                        document.body.removeChild(notification);
                    }
                }, 300);
            }, 4000);
        }
        
        // ✅ ATTACHER ÉVÉNEMENTS AUX BOUTONS
        function attachRelayEvents() {
            const buttons = document.querySelectorAll('.select-relay-btn');
            console.log(`🔗 Attachement événements à ${buttons.length} boutons`);
            
            buttons.forEach((btn, index) => {
                // Supprimer les anciens événements
                btn.onclick = null;
                
                btn.onclick = function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    console.log(`🖱️ Clic bouton ${index + 1}`);
                    
                    try {
                        const pointDataRaw = this.getAttribute('data-point');
                        console.log('📊 Data brute:', pointDataRaw?.substring(0, 100) + '...');
                        
                        if (!pointDataRaw) {
                            throw new Error('Données point manquantes');
                        }
                        
                        // Parser les données en gérant l'échappement
                        const pointData = JSON.parse(pointDataRaw.replace(/&apos;/g, "'"));
                        console.log('📦 Point parsé:', pointData.name, pointData.id);
                        
                        // Appeler la fonction de sélection
                        selectRelayPoint(pointData);
                        
                    } catch (error) {
                        console.error('❌ Erreur clic bouton:', error);
                        alert('Erreur lors de la sélection: ' + error.message);
                    }
                };
            });
            
            console.log(`✅ Événements attachés à ${buttons.length} boutons`);
        }
        
        // ✅ FONCTION DE RECHERCHE DE POINTS
        function searchRelayPoints() {
            const postalCode = relayPostalCode.value.trim();
            
            if (!postalCode || postalCode.length !== 5 || !/^\d{5}$/.test(postalCode)) {
                alert('Veuillez saisir un code postal valide (5 chiffres)');
                relayPostalCode.focus();
                return;
            }
            
            console.log('🔍 Recherche points relais pour:', postalCode);
            
            searchRelayBtn.textContent = 'Recherche...';
            searchRelayBtn.disabled = true;
            
            relayList.innerHTML = `
            <div style="background: rgba(52, 152, 219, 0.15); border: 1px solid rgba(52, 152, 219, 0.2);
                        border-radius: 16px; padding: 24px; text-align: center; color: #3498db; font-weight: 600;">
                🔍 Recherche des points relais...
            </div>
        `;
            relayResults.style.display = 'block';
            
            const city = relayCity ? relayCity.value.trim() : '';
            
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
                console.log('📡 Réponse API:', data);
                
                if (data.success && data.data && data.data.points.length > 0) {
                    currentRelayPoints = data.data.points;
                    displayRelayPoints(data.data.points);
                    console.log(`✅ ${data.data.points.length} points trouvés et affichés`);
                } else {
                    const message = data.message || 'Aucun point relais trouvé pour ce code postal';
                    relayList.innerHTML = `
                    <div style="background: rgba(231, 76, 60, 0.15); border: 1px solid rgba(231, 76, 60, 0.2);
                                border-radius: 16px; padding: 20px; text-align: center; color: #e74c3c; font-weight: 600;">
                        ❌ ${message}
                    </div>
                `;
                    currentRelayPoints = [];
                    console.log('❌ Aucun point trouvé');
                }
            })
            .catch(error => {
                console.error('❌ Erreur API:', error);
                relayList.innerHTML = `
                <div style="background: rgba(231, 76, 60, 0.15); border: 1px solid rgba(231, 76, 60, 0.2);
                            border-radius: 16px; padding: 20px; text-align: center; color: #e74c3c; font-weight: 600;">
                    ⚠️ Erreur de connexion. Veuillez réessayer.
                </div>
            `;
                currentRelayPoints = [];
            })
            .finally(() => {
                searchRelayBtn.textContent = 'Rechercher';
                searchRelayBtn.disabled = false;
            });
        }
        
        // ✅ AFFICHER LES POINTS RELAIS
        function displayRelayPoints(points) {
            console.log(`📋 Affichage de ${points.length} points`);
            
            let html = `
            <h5 style="margin-bottom: 20px; color: var(--astro-black); font-weight: 700; text-align: center;
                       background: rgba(255,255,255,0.1); padding: 12px 20px; border-radius: 12px;
                       backdrop-filter: blur(5px); border: 1px solid rgba(255,255,255,0.15);">
                📍 Points de collecte disponibles
            </h5>
        `;
            
            // Séparer les types de points
            const relayPoints = points.filter(p => p.type === 'REL');
            const lockers = points.filter(p => p.type === 'LOC');
            
            // Afficher les casiers en premier
            if (lockers.length > 0) {
                html += `<h6 style="margin: 20px 0 15px 0; color: #27ae60; font-weight: 700;
                             background: rgba(39, 174, 96, 0.1); padding: 10px 16px; border-radius: 12px;
                             border-left: 4px solid #27ae60;">🔒 Casiers automatiques (24h/24)</h6>`;
                lockers.forEach(point => {
                    html += generatePointHTML(point, '#27ae60', '🔒');
                });
            }
            
            // Puis les points relais
            if (relayPoints.length > 0) {
                html += `<h6 style="margin: 20px 0 15px 0; color: #ffffffff; font-weight: 700;
                             background: rgba(107, 107, 107, 0.1); padding: 10px 16px; border-radius: 12px;">🏪 Points relais</h6>`;
                relayPoints.forEach(point => {
                    html += generatePointHTML(point, '#ffffffff', '🏪');
                });
            }
            
            relayList.innerHTML = html;
            relayResults.style.display = 'block';
            
            // Attacher les événements aux nouveaux boutons
            setTimeout(() => {
                attachRelayEvents();
            }, 100);
        }
        
        // ✅ GÉNÉRER HTML POUR UN POINT
        function generatePointHTML(point, color, icon) {
            const isLocker = point.type === 'LOC';
            const typeLabel = isLocker ? 'Casier automatique' : 'Point relais';
            const availabilityText = isLocker ? 'Accès 24h/24 - 7j/7' : 'Voir horaires sur place';
            const accentColor = color === '#ffffffff' ? 'rgba(39, 174, 96, 0.7)' : 'rgba(52, 152, 219, 0.7)';
            
            // Échapper les données JSON pour éviter les erreurs
            const escapedPoint = JSON.stringify(point).replace(/'/g, "&apos;");
            
            return `
            <div class="relay-point" data-point-id="${point.id}" style="
                background: rgba(255, 255, 255, 0.15); border: 1px solid rgba(255, 255, 255, 0.2);
                border-radius: 16px; backdrop-filter: blur(10px); padding: 20px; margin-bottom: 12px;
                cursor: pointer; transition: all 0.3s ease;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                    <div style="flex: 1;">
                        <h4 style="margin: 0 0 8px 0; color: var(--astro-black); font-weight: 700; font-size: 16px;">
                            ${icon} ${point.name}
                        </h4>
                        <p style="margin: 0 0 8px 0; color: rgba(0,0,0,0.7); font-weight: 500;">
                            ${point.address}
                        </p>
                        <p style="margin: 0 0 8px 0; color: rgba(0,0,0,0.7); font-weight: 500;">
                            ${point.postal_code} ${point.city}
                        </p>
                        <div style="margin-top: 10px;">
                            <small style="color: #ffffffff; font-weight: 600;
                                          padding: 4px 8px; border-radius: 8px;">⏰ ${availabilityText}</small>
                        </div>
                        ${point.phone ? `<small style="color: rgba(0,0,0,0.6); display: block; margin-top: 6px;">📞 ${point.phone}</small>` : ''}
                    </div>
                    <button type="button" class="select-relay-btn" data-point='${escapedPoint}' style="
                        background:rgba(255, 255, 255, 0.15); 
                        color: white; 
                        border: 1px solid rgba(255, 255, 255, 0.2);
                        padding: 12px 18px; 
                        border-radius: 12px; 
                        cursor: pointer; 
                        font-size: 13px;
                        font-weight: 600; 
                        transition: all 0.3s ease; 
                        margin-left: 15px; 
                        min-width: 100px;">
                        Sélectionner
                    </button>
                </div>
            </div>
        `;
        }
        
        // ✅ FONCTIONS UTILITAIRES
        function updateTotal() {
            const shipping = homeShipping.checked ? 6.99 : 4.99;
            const total = baseTotal + shipping;
            shippingCost.textContent = shipping.toFixed(2) + '€';
            finalTotal.textContent = total.toFixed(2) + '€';
        }
        
        function updateSelection() {
            options.forEach(option => option.classList.remove('selected'));
            if (homeShipping.checked) {
                homeShipping.closest('.checkout-option').classList.add('selected');
                mondialRelayWidget.style.display = 'none';
            } else {
                pickupShipping.closest('.checkout-option').classList.add('selected');
                mondialRelayWidget.style.display = 'block';
            }
        }
        
        // ✅ ÉVÉNEMENTS PRINCIPAUX
        if (homeShipping) homeShipping.addEventListener('change', function() { updateTotal(); updateSelection(); });
        if (pickupShipping) pickupShipping.addEventListener('change', function() { updateTotal(); updateSelection(); });
        if (searchRelayBtn) searchRelayBtn.addEventListener('click', searchRelayPoints);
        
        if (relayPostalCode) {
            relayPostalCode.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    searchRelayPoints();
                }
            });
        }
        
        // Permettre de cliquer sur l'option entière
        options.forEach(option => {
            option.addEventListener('click', function() {
                const radio = this.querySelector('input[type="radio"]');
                if (radio) {
                    radio.checked = true;
                    updateTotal();
                    updateSelection();
                }
            });
        });
        
        // Pré-remplir les champs
        const shippingPostalCode = document.getElementById('shipping_postal_code');
        const shippingCity = document.getElementById('shipping_city');
        if (shippingPostalCode && shippingPostalCode.value && relayPostalCode) {
            relayPostalCode.value = shippingPostalCode.value;
        }
        if (shippingCity && shippingCity.value && relayCity) {
            relayCity.value = shippingCity.value;
        }
        
        // ✅ INITIALISATION
        updateTotal();
        updateSelection();
        
        console.log('✅ Script points relais initialisé avec succès');
    });
</script>
@endsection
