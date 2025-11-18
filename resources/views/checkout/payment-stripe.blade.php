@extends('layouts.app')

@section('content')
<div class="checkout-container">
    <!-- En-tête checkout -->
    <div class="checkout-header">
        <h1 class="checkout-title">ASTROLAB</h1>
        <h2 class="checkout-subtitle">Paiement Sécurisé</h2>
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
                <h3 class="section-title">Livraison</h3>
                <div class="info-summary">
                    <p><strong>{{ $checkoutData['contact']['first_name'] }} {{ $checkoutData['contact']['last_name'] }}</strong></p>
                    <p>{{ $checkoutData['shipping']['address'] }}</p>
                    <p>{{ $checkoutData['shipping']['postal_code'] }} {{ $checkoutData['shipping']['city'] }}</p>
                    <p>{{ $checkoutData['shipping']['country'] }}</p>
                </div>
            </div>

            <!-- Mode de livraison -->
            <div class="checkout-section">
                <h3 class="section-title">Mode de livraison</h3>
                <div class="delivery-summary">
                    @if($checkoutData['shipping']['mode'] === 'relay')
                        <p><strong>Point Relais</strong> - {{ $checkoutData['shipping']['relay']['Nom'] }}</p>
                        <p>{{ $checkoutData['shipping']['relay']['Adresse1'] }}</p>
                        <p>{{ $checkoutData['shipping']['relay']['CP'] }} {{ $checkoutData['shipping']['relay']['Ville'] }}</p>
                    @else
                        <p><strong>Livraison à domicile</strong></p>
                    @endif
                </div>
            </div>

            <!-- NOUVELLE SECTION PAIEMENT STRIPE -->
            <div class="checkout-section">
                <h3 class="section-title">
                    <i class="fas fa-lock"></i>
                    Paiement Sécurisé
                </h3>
                
                <div id="payment-status" class="payment-status hidden">
                    <div class="loading">
                        <i class="fas fa-spinner fa-spin"></i>
                        <span>Traitement en cours...</span>
                    </div>
                </div>

                <!-- FORMULAIRE STRIPE -->
                <form id="payment-form" class="payment-form">
                    @csrf
                    
                    <!-- Stripe Elements Container -->
                    <div id="card-element" class="stripe-card-element">
                        <!-- Stripe Elements sera monté ici -->
                    </div>
                    
                    <!-- Erreurs Stripe -->
                    <div id="card-errors" class="payment-errors" role="alert"></div>
                    
                    <div class="payment-actions">
                        <button id="submit-payment" class="btn-payment" type="submit">
                            <i class="fas fa-credit-card"></i>
                            <span class="button-text">Payer {{ number_format($total, 2) }}€</span>
                            <div class="payment-loader hidden">
                                <i class="fas fa-spinner fa-spin"></i>
                            </div>
                        </button>
                        
                        <a href="{{ route('checkout.shipping') }}" class="btn-back">
                            <i class="fas fa-arrow-left"></i>
                            Retour
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Récapitulatif commande -->
        <div class="checkout-sidebar">
            <div class="order-summary">
                <h3 class="summary-title">Récapitulatif</h3>
                
                <!-- Articles -->
                <div class="order-items">
                    @foreach($cart->getItems() as $item)
                        <div class="order-item">
                            @if($item->product->getFirstMediaUrl('products'))
                                <img src="{{ $item->product->getFirstMediaUrl('products', 'thumb') }}" 
                                     alt="{{ $item->product->name }}" 
                                     class="item-image">
                            @else
                                <div class="item-image-placeholder">
                                    <i class="fas fa-image"></i>
                                </div>
                            @endif
                            
                            <div class="item-details">
                                <h4 class="item-name">{{ $item->product->name }}</h4>
                                <p class="item-options">
                                    @if($item->size)
                                        Taille: {{ $item->size->getLabel() }}
                                    @endif
                                    @if($item->color)
                                        | Couleur: {{ $item->color }}
                                    @endif
                                </p>
                                <div class="item-pricing">
                                    <span class="item-quantity">{{ $item->quantity }}x</span>
                                    <span class="item-price">{{ number_format($item->price, 2) }}€</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Totaux -->
                <div class="order-totals">
                    <div class="total-line">
                        <span>Sous-total</span>
                        <span>{{ number_format($cart->getSubtotal(), 2) }}€</span>
                    </div>
                    
                    <div class="total-line">
                        <span>Livraison</span>
                        <span>{{ number_format($cart->getShippingCost(), 2) }}€</span>
                    </div>
                    
                    <div class="total-line final">
                        <span>Total</span>
                        <span>{{ number_format($total, 2) }}€</span>
                    </div>
                </div>

                <!-- Sécurité -->
                <div class="security-info">
                    <div class="security-badges">
                        <div class="badge">
                            <i class="fas fa-lock"></i>
                            <span>Paiement sécurisé SSL</span>
                        </div>
                        <div class="badge">
                            <i class="fab fa-stripe"></i>
                            <span>Powered by Stripe</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- STRIPE JAVASCRIPT -->
<script src="https://js.stripe.com/v3/"></script>
<script>
// Configuration Stripe
const stripe = Stripe('{{ config("stripe.publishable_key") }}');
const elements = stripe.elements({
    appearance: {
        theme: 'night',
        variables: {
            colorPrimary: '#ffffff',
            colorBackground: 'rgba(255, 255, 255, 0.05)',
            colorText: '#ffffff',
            colorDanger: '#dc3545',
            fontFamily: 'Bebas Kai, sans-serif',
            borderRadius: '8px',
        }
    }
});

// Créer l'élément card
const cardElement = elements.create('card', {
    style: {
        base: {
            fontSize: '16px',
            color: '#ffffff',
            fontFamily: 'Bebas Kai, sans-serif',
            '::placeholder': {
                color: '#9ca3af',
            },
        },
    },
});

cardElement.mount('#card-element');

// Gestion des erreurs
cardElement.on('change', function(event) {
    const displayError = document.getElementById('card-errors');
    if (event.error) {
        displayError.textContent = event.error.message;
        displayError.classList.remove('hidden');
    } else {
        displayError.textContent = '';
        displayError.classList.add('hidden');
    }
});

// Variables globales
let processing = false;

// Soumission du formulaire
document.getElementById('payment-form').addEventListener('submit', async function(event) {
    event.preventDefault();
    
    if (processing) return;
    processing = true;
    
    const submitButton = document.getElementById('submit-payment');
    const buttonText = submitButton.querySelector('.button-text');
    const loader = submitButton.querySelector('.payment-loader');
    
    // État loading
    submitButton.disabled = true;
    buttonText.classList.add('hidden');
    loader.classList.remove('hidden');
    
    try {
        // Étape 1: Créer le PaymentIntent côté serveur
        console.log('🚀 Création du PaymentIntent...');
        const response = await fetch('{{ route("checkout.process") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                payment_method: 'card'
            })
        });
        
        const paymentResult = await response.json();
        console.log('📦 Réponse serveur:', paymentResult);
        
        if (!paymentResult.success) {
            throw new Error(paymentResult.error || 'Erreur serveur');
        }
        
        if (!paymentResult.client_secret) {
            throw new Error('Client secret manquant');
        }
        
        // Étape 2: Confirmer le paiement côté client
        console.log('🔐 Confirmation avec Stripe...');
        const {error, paymentIntent} = await stripe.confirmCardPayment(paymentResult.client_secret, {
            payment_method: {
                card: cardElement,
                billing_details: {
                    name: '{{ $checkoutData["contact"]["first_name"] }} {{ $checkoutData["contact"]["last_name"] }}',
                    email: '{{ $checkoutData["contact"]["email"] }}'
                }
            }
        });
        
        if (error) {
            console.error('❌ Erreur Stripe:', error);
            throw new Error(error.message);
        }
        
        console.log('✅ PaymentIntent confirmé:', paymentIntent);
        
        // Étape 3: Notifier le serveur du succès
        const confirmResponse = await fetch('{{ route("checkout.confirm-payment") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                payment_intent_id: paymentIntent.id,
                payment_method_id: paymentIntent.payment_method
            })
        });
        
        const confirmResult = await confirmResponse.json();
        console.log('🎉 Confirmation finale:', confirmResult);
        
        if (confirmResult.success) {
            // Redirection vers la page de succès
            window.location.href = confirmResult.redirect_url;
        } else {
            throw new Error(confirmResult.error || 'Erreur de confirmation');
        }
        
    } catch (error) {
        console.error('💥 Erreur paiement:', error);
        
        // Affichage de l'erreur
        const errorElement = document.getElementById('card-errors');
        errorElement.textContent = error.message;
        errorElement.classList.remove('hidden');
        
        // Reset du bouton
        submitButton.disabled = false;
        buttonText.classList.remove('hidden');
        loader.classList.add('hidden');
        processing = false;
    }
});
</script>

<style>
/* Styles spécifiques au paiement Stripe */
.stripe-card-element {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 15px;
    transition: border-color 0.3s ease;
}

.stripe-card-element:hover {
    border-color: rgba(255, 255, 255, 0.2);
}

.stripe-card-element:focus-within {
    border-color: #ffffff;
    box-shadow: 0 0 0 2px rgba(255, 255, 255, 0.1);
}

.payment-errors {
    color: #dc3545;
    font-size: 14px;
    margin-bottom: 15px;
    padding: 10px;
    background: rgba(220, 53, 69, 0.1);
    border: 1px solid rgba(220, 53, 69, 0.2);
    border-radius: 6px;
}

.payment-errors.hidden {
    display: none;
}

.btn-payment {
    background: linear-gradient(135deg, #ffffff, #f0f0f0);
    color: #222;
    border: none;
    padding: 15px 30px;
    border-radius: 8px;
    font-weight: bold;
    font-family: 'Bebas Kai', sans-serif;
    font-size: 1.1rem;
    letter-spacing: 1px;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    width: 100%;
    margin-bottom: 15px;
    position: relative;
}

.btn-payment:hover {
    background: linear-gradient(135deg, #f0f0f0, #e0e0e0);
    transform: translateY(-2px);
}

.btn-payment:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
}

.payment-loader {
    position: absolute;
    left: 50%;
    top: 50%;
    transform: translate(-50%, -50%);
}

.payment-loader.hidden {
    display: none;
}

.button-text.hidden {
    visibility: hidden;
}

.security-badges {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.security-badges .badge {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 12px;
    color: rgba(255, 255, 255, 0.7);
}

.payment-status {
    text-align: center;
    padding: 20px;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 8px;
    margin-bottom: 20px;
}

.payment-status.hidden {
    display: none;
}

.loading {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    color: #ffffff;
}
</style>
@endsection
