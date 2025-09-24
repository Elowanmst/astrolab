@extends('layouts.app')

@section('content')
<div class="checkout-container">
    <!-- En-tête checkout -->
    <div class="checkout-header">
        <h1 class="checkout-title">ASTROLAB</h1>
        <h2 class="checkout-subtitle">Paiement</h2>
        <p class="checkout-step">| ÉTAPE 3/3 : FINALISATION |</p>
        
        <div class="progress-bar step-3"></div>
    </div>

    @if ($errors->any())
        <div class="checkout-alert error">
            <i class="fas fa-exclamation-triangle"></i>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="checkout-grid">
        
        <!-- Informations et articles -->
        <div class="checkout-main">
            
            <!-- Informations de livraison -->
            <div class="checkout-section">
                <h3 class="checkout-section-title">
                    <i class="fas fa-truck"></i>
                    Adresse de livraison
                </h3>
                <div class="checkout-info-display">
                    <div class="checkout-info-row">
                        <strong>{{ $shippingData['shipping_name'] }}</strong>
                    </div>
                    <div class="checkout-info-row">{{ $shippingData['shipping_email'] }}</div>
                    <div class="checkout-info-row">{{ $shippingData['shipping_address'] }}</div>
                    <div class="checkout-info-row">{{ $shippingData['shipping_postal_code'] }} {{ $shippingData['shipping_city'] }}</div>
                    <div class="checkout-info-row highlight">
                        <span>Mode de livraison:</span>
                        <span>{{ $shippingData['shipping_method'] === 'home' ? 'Livraison à domicile' : 'Point relais' }} ({{ number_format($shippingCost, 2) }}€)</span>
                    </div>
                </div>
            </div>

            <!-- Articles commandés -->
            <div class="checkout-section">
                <h3 class="checkout-section-title">
                    <i class="fas fa-shopping-bag"></i>
                    Articles commandés
                </h3>
                <div class="checkout-items-list">
                    @foreach($cart->get() as $item)
                        <div class="checkout-item detailed">
                            <div class="checkout-item-image">
                                @if($item['image'])
                                    <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}">
                                @else
                                    <div class="checkout-item-placeholder">NO IMG</div>
                                @endif
                            </div>
                            <div class="checkout-item-info">
                                <div class="checkout-item-name">{{ $item['name'] }}</div>
                                <div class="checkout-item-details">
                                    @if($item['size'])
                                        <div>Taille: {{ $item['size'] }}</div>
                                    @endif
                                    @if($item['color'])
                                        <div>Couleur: {{ $item['color'] }}</div>
                                    @endif
                                </div>
                            </div>
                            <div class="checkout-item-pricing">
                                <div class="checkout-item-quantity">{{ $item['quantity'] }} x {{ $item['price'] }}€</div>
                                <div class="checkout-item-total">{{ number_format($item['price'] * $item['quantity'], 2) }}€</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Formulaire de paiement -->
            <div class="checkout-section">
                <h3 class="checkout-section-title">
                    <i class="fas fa-credit-card"></i>
                    Informations de paiement
                </h3>
                
                <form action="{{ route('checkout.process') }}" method="POST" id="payment-form">
                    @csrf
                    
                    <div class="checkout-payment-method">
                        <input type="radio" name="payment_method" id="payment_card" value="card" checked>
                        <label for="payment_card">
                            <i class="fas fa-credit-card"></i>
                            <span>Carte bancaire</span>
                            <div class="payment-badges">
                                <span class="payment-badge visa">VISA</span>
                                <span class="payment-badge mastercard">MC</span>
                                <span class="payment-badge cb">CB</span>
                            </div>
                        </label>
                    </div>

                    <div class="checkout-form-group">
                        <label for="card_number" class="checkout-label">Numéro de carte *</label>
                        <input type="text" name="card_number" id="card_number" placeholder="1234 5678 9012 3456" maxlength="19" required class="checkout-input">
                    </div>

                    <div class="checkout-form-group">
                        <div class="checkout-form-grid">
                            <div>
                                <label for="card_expiry" class="checkout-label">Date d'expiration *</label>
                                <input type="text" name="card_expiry" id="card_expiry" placeholder="MM/AA" maxlength="5" required class="checkout-input">
                            </div>
                            
                            <div>
                                <label for="card_cvv" class="checkout-label">Code CVV *</label>
                                <input type="text" name="card_cvv" id="card_cvv" placeholder="123" maxlength="4" required class="checkout-input">
                            </div>
                        </div>
                    </div>

                    <div class="checkout-form-group">
                        <label for="card_name" class="checkout-label">Nom sur la carte *</label>
                        <input type="text" name="card_name" id="card_name" value="{{ $shippingData['shipping_name'] }}" required class="checkout-input">
                    </div>
                </form>
            </div>
        </div>

        <!-- Récapitulatif et validation -->
        <div class="checkout-summary">
            <div class="checkout-section">
                <h3 class="checkout-section-title">
                    <i class="fas fa-calculator"></i>
                    Récapitulatif
                </h3>
                
                <div class="checkout-totals">
                    <div class="checkout-total-line">
                        <span>Sous-total:</span>
                        <span>{{ number_format($total, 2) }}€</span>
                    </div>
                    <div class="checkout-total-line">
                        <span>Livraison:</span>
                        <span>{{ number_format($shippingCost, 2) }}€</span>
                    </div>
                    <div class="checkout-total-line final">
                        <span>TOTAL À PAYER:</span>
                        <span>{{ number_format($finalTotal, 2) }}€</span>
                    </div>
                </div>

                <div class="checkout-terms">
                    <div class="checkout-checkbox">
                        <input type="checkbox" id="terms" required>
                        <label for="terms">
                            J'accepte les <a href="{{ route('legal.cgv') }}" target="_blank">conditions générales de vente</a>
                        </label>
                    </div>
                </div>

                <button type="submit" form="payment-form" id="pay-button" class="checkout-btn payment">
                    <i class="fas fa-lock"></i>
                    Payer {{ number_format($finalTotal, 2) }}€
                </button>

                <div class="checkout-security-info">
                    <i class="fas fa-shield-alt"></i>
                    Paiement sécurisé SSL
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <div class="checkout-navigation">
        <a href="{{ route('checkout.shipping') }}" class="checkout-btn secondary">
            <i class="fas fa-arrow-left"></i>
            Retour aux informations de livraison
        </a>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Formatage du numéro de carte
    const cardNumber = document.getElementById('card_number');
    cardNumber.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\s/g, '').replace(/[^0-9]/gi, '');
        let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
        e.target.value = formattedValue;
    });

    // Formatage de la date d'expiration
    const cardExpiry = document.getElementById('card_expiry');
    cardExpiry.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length >= 2) {
            value = value.substring(0, 2) + '/' + value.substring(2, 4);
        }
        e.target.value = value;
    });

    // Validation des champs CVV (seulement des chiffres)
    const cardCvv = document.getElementById('card_cvv');
    cardCvv.addEventListener('input', function(e) {
        e.target.value = e.target.value.replace(/[^0-9]/gi, '');
    });

    // Validation du formulaire
    const form = document.getElementById('payment-form');
    const payButton = document.getElementById('pay-button');
    
    form.addEventListener('submit', function(e) {
        payButton.disabled = true;
        payButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Traitement en cours...';
        payButton.classList.add('loading');
    });
});
</script>
@endsection
