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
                                    <h4>Point relais</h4>
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
                                Choisissez votre point relais
                            </h4>
                            
                            <div class="relay-search">
                                <div class="checkout-form-group">
                                    <label for="relay-postal-code" class="checkout-label">Code postal</label>
                                    <input type="text" 
                                           id="relay-postal-code" 
                                           placeholder="Ex: 75001"
                                           class="checkout-input"
                                           style="max-width: 200px;">
                                    <button type="button" id="search-relay-btn" class="btn btn-sm btn-primary" style="margin-left: 10px;">
                                        Rechercher
                                    </button>
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
    const relayResults = document.getElementById('relay-results');
    const relayList = document.getElementById('relay-list');
    const selectedRelayPoint = document.getElementById('selected-relay-point');
    
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
        if (!postalCode) {
            alert('Veuillez saisir un code postal');
            return;
        }
        
        searchRelayBtn.textContent = 'Recherche...';
        searchRelayBtn.disabled = true;
        
        fetch(`/api/mondial-relay/relay-points/search?postal_code=${postalCode}&city=`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.points.length > 0) {
                    displayRelayPoints(data.points);
                } else {
                    relayList.innerHTML = '<p style="color: #e74c3c;">Aucun point relais trouvé pour ce code postal.</p>';
                    relayResults.style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                relayList.innerHTML = '<p style="color: #e74c3c;">Erreur lors de la recherche. Veuillez réessayer.</p>';
                relayResults.style.display = 'block';
            })
            .finally(() => {
                searchRelayBtn.textContent = 'Rechercher';
                searchRelayBtn.disabled = false;
            });
    }
    
    // Afficher la liste des points relais
    function displayRelayPoints(points) {
        let html = '<h5 style="margin-bottom: 15px;">Points relais disponibles :</h5>';
        
        points.forEach(point => {
            html += `
                <div class="relay-point" data-point-id="${point.id}" style="
                    border: 1px solid #ddd; 
                    padding: 15px; 
                    margin-bottom: 10px; 
                    cursor: pointer;
                    border-radius: 5px;
                    transition: all 0.3s ease;
                " onmouseover="this.style.backgroundColor='#f8f9fa'" onmouseout="this.style.backgroundColor='white'">
                    <div style="display: flex; justify-content: space-between; align-items: start;">
                        <div style="flex: 1;">
                            <h6 style="margin: 0 0 5px 0; font-weight: bold; color: #2c3e50;">${point.name}</h6>
                            <p style="margin: 0 0 5px 0; color: #7f8c8d;">${point.address}</p>
                            <p style="margin: 0; color: #7f8c8d;">${point.postal_code} ${point.city}</p>
                            ${point.distance > 0 ? `<small style="color: #3498db;">Distance: ${point.distance}m</small>` : ''}
                        </div>
                        <button type="button" class="select-relay-btn" data-point='${JSON.stringify(point)}' style="
                            background: #3498db;
                            color: white;
                            border: none;
                            padding: 8px 16px;
                            border-radius: 4px;
                            cursor: pointer;
                            font-size: 12px;
                        ">Sélectionner</button>
                    </div>
                </div>
            `;
        });
        
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
    
    // Sélectionner un point relais
    function selectRelayPoint(point) {
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
        alert(`Point relais sélectionné : ${point.name}\n${point.address}\n${point.postal_code} ${point.city}`);
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
    
    // Pré-remplir le code postal si l'adresse est déjà saisie
    const shippingPostalCode = document.getElementById('shipping_postal_code');
    if (shippingPostalCode && shippingPostalCode.value) {
        relayPostalCode.value = shippingPostalCode.value;
    }
});
</script>
</script>
@endsection
