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
                    @if($shippingData['shipping_phone'] ?? false)
                        <div class="checkout-info-row">{{ $shippingData['shipping_phone'] }}</div>
                    @endif
                    <div class="checkout-info-row">{{ $shippingData['shipping_address'] }}</div>
                    <div class="checkout-info-row">{{ $shippingData['shipping_postal_code'] }} {{ $shippingData['shipping_city'] }}</div>
                    <div class="checkout-info-row highlight">
                        <span>Mode de livraison:</span>
                        <span>{{ $shippingData['shipping_method'] === 'home' ? 'Livraison à domicile' : 'Point relais' }} ({{ number_format($shippingCost, 2) }}€)</span>
                    </div>
                    
                    @if($shippingData['shipping_method'] === 'pickup' && isset($shippingData['relay_point_name']))
                        <!-- Informations du point relais sélectionné -->
                        <div class="checkout-relay-info">
                            <div class="checkout-info-row">
                                <i class="fas fa-map-marker-alt"></i>
                                <strong>Point relais sélectionné:</strong>
                            </div>
                            <div class="checkout-relay-details">
                                <div class="checkout-info-row">
                                    <strong>{{ $shippingData['relay_point_name'] }}</strong>
                                </div>
                                @if($shippingData['relay_point_address'] ?? false)
                                    <div class="checkout-info-row">{{ $shippingData['relay_point_address'] }}</div>
                                @endif
                                @if(($shippingData['relay_point_postal_code'] ?? false) && ($shippingData['relay_point_city'] ?? false))
                                    <div class="checkout-info-row">{{ $shippingData['relay_point_postal_code'] }} {{ $shippingData['relay_point_city'] }}</div>
                                @endif
                                @if($shippingData['relay_point_id'] ?? false)
                                    <div class="checkout-info-row">
                                        <small>Code: {{ $shippingData['relay_point_id'] }}</small>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
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
                                <!-- Visa -->
                                <div class="payment-badge visa">
                                    <svg width="40" height="24" viewBox="0 0 40 24" fill="none">
                                        <rect width="40" height="24" rx="4" fill="white"/>
                                        <path d="M16.1533 8.99707H14.3301L12.7266 16.5742H14.5498L16.1533 8.99707Z" fill="#1A1F71"/>
                                        <path d="M22.9805 9.12598C22.5879 8.97266 21.9443 8.8125 21.0537 8.8125C19.334 8.8125 18.1309 9.6709 18.124 10.8877C18.1172 11.7734 18.9893 12.2637 19.6465 12.5469C20.3174 12.835 20.5596 13.0225 20.5566 13.2852C20.5527 13.7041 19.9893 13.8975 19.4707 13.8975C18.7539 13.8975 18.3613 13.791 17.7451 13.5352L17.4824 13.4189L17.1992 15.1631C17.6904 15.3779 18.5918 15.5654 19.5273 15.5732C21.3623 15.5732 22.5459 14.7285 22.5547 13.4365C22.5615 12.7832 22.1523 12.2969 21.252 11.877C20.6436 11.6074 20.2656 11.4229 20.2686 11.1328C20.2686 10.8789 20.5723 10.6123 21.2559 10.6123C21.8223 10.6016 22.2451 10.7324 22.5547 10.8506L22.6885 10.9111L22.9805 9.12598Z" fill="#1A1F71"/>
                                        <path d="M27.1436 8.99707H25.7539C25.3037 8.99707 24.9668 9.12305 24.7969 9.58496L21.9844 16.5742H23.8174C23.8174 16.5742 24.1416 15.752 24.2119 15.5586H26.6689C26.7217 15.8281 26.8848 16.5742 26.8848 16.5742H28.4678L27.1436 8.99707ZM24.8887 14.0957L25.9189 11.2959L26.4678 14.0957H24.8887Z" fill="#1A1F71"/>
                                        <path d="M13.5391 8.99707L11.7598 14.6348L11.5762 13.7344C11.2178 12.6123 10.1123 11.3818 8.85742 10.7793L10.4883 16.5684H12.3281L15.3789 8.99707H13.5391Z" fill="#1A1F71"/>
                                        <path d="M9.70312 8.99707H6.85742L6.82617 9.1377C9.2168 9.7666 10.7998 11.2578 11.5762 13.7344L10.7461 10.0234C10.6318 9.54883 10.3057 9.02246 9.70312 8.99707Z" fill="#F7B600"/>
                                    </svg>
                                </div>
                                
                                <!-- Mastercard -->
                                <div class="payment-badge mastercard">
                                    <svg width="40" height="24" viewBox="0 0 40 24" fill="none">
                                        <rect width="40" height="24" rx="4" fill="white"/>
                                        <circle cx="15" cy="12" r="7" fill="#EB001B"/>
                                        <circle cx="25" cy="12" r="7" fill="#F79E1B"/>
                                        <path d="M20 6.5C21.5 7.8 22.5 9.8 22.5 12C22.5 14.2 21.5 16.2 20 17.5C18.5 16.2 17.5 14.2 17.5 12C17.5 9.8 18.5 7.8 20 6.5Z" fill="#FF5F00"/>
                                    </svg>
                                </div>
                                
                                <!-- Carte Bleue (CB) -->
                                <div class="payment-badge cb">
                                    <svg width="40" height="24" viewBox="0 0 40 24" fill="none">
                                        <rect width="40" height="24" rx="4" fill="#005CA9"/>
                                        <path d="M8 8H12V16H8V8Z" fill="white"/>
                                        <path d="M14 8H32V10H14V8Z" fill="white"/>
                                        <path d="M14 11H28V13H14V11Z" fill="white"/>
                                        <path d="M14 14H24V16H14V14Z" fill="white"/>
                                        <text x="20" y="20" font-family="Arial, sans-serif" font-size="6" fill="white" text-anchor="middle">CB</text>
                                    </svg>
                                </div>
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
