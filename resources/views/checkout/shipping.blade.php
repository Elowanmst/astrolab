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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const homeShipping = document.getElementById('shipping_home');
    const pickupShipping = document.getElementById('shipping_pickup');
    const shippingCost = document.getElementById('shipping-cost');
    const finalTotal = document.getElementById('final-total');
    const options = document.querySelectorAll('.checkout-option');
    
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
        } else {
            pickupShipping.closest('.checkout-option').classList.add('selected');
        }
    }
    
    homeShipping.addEventListener('change', function() {
        updateTotal();
        updateSelection();
    });
    
    pickupShipping.addEventListener('change', function() {
        updateTotal();
        updateSelection();
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
});
</script>
@endsection
