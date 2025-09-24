@extends('layouts.app')

@section('content')
<div class="checkout-container">
    <!-- En-tête checkout -->
    <div class="checkout-header">
        <h1 class="checkout-title">ASTROLAB</h1>
        <h2 class="checkout-subtitle">Finaliser ma commande</h2>
        <p class="checkout-step">| ÉTAPE 1/3 : IDENTIFICATION |</p>
        
        <div class="progress-bar step-1"></div>
    </div>

    <div class="checkout-grid">
        
        <!-- Résumé du panier -->
        <div class="checkout-summary">
            <div class="checkout-section">
                <h3 class="checkout-section-title">Mon panier</h3>
                
                @foreach($cart->get() as $item)
                    <div class="checkout-item">
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
                                <div>{{ $item['quantity'] }} x {{ $item['price'] }}€</div>
                            </div>
                        </div>
                    </div>
                @endforeach
                
                <div class="checkout-totals">
                    <div class="checkout-total-line">
                        <span>Total HT:</span>
                        <span>{{ number_format($cart->getTotalHT(), 2) }}€</span>
                    </div>
                    <div class="checkout-total-line final">
                        <span>Total:</span>
                        <span>{{ number_format($cart->getTotal(), 2) }}€</span>
                    </div>
                    <p style="color: var(--astro-text-secondary); font-size: 0.8rem; margin-top: 0.5rem; font-weight: 500;">+ Frais de livraison (calculés à l'étape suivante)</p>
                </div>
            </div>
        </div>

        <!-- Options de connexion -->
        <div class="checkout-main">
            
            @auth
                <!-- Utilisateur déjà connecté -->
                <div class="checkout-section" style="border-color: var(--astro-success); background: rgba(40, 167, 69, 0.1);">
                    <h3 class="checkout-section-title" style="color: var(--astro-success);">Connecté en tant que</h3>
                    <p style="color: var(--astro-success); margin-bottom: 1.5rem; font-weight: 500;">{{ Auth::user()->name }} ({{ Auth::user()->email }})</p>
                    
                    <form action="{{ route('checkout.shipping') }}" method="GET">
                        <button type="submit" class="checkout-btn" style="background: var(--astro-success); color: white;">
                            <i class="fas fa-user-check"></i>
                            Continuer avec ce compte
                        </button>
                    </form>
                </div>
            @else
                <!-- Créer un compte -->
                <div class="checkout-section">
                    <h3 class="checkout-section-title">Créer un compte</h3>
                    <p style="color: var(--astro-text-secondary); margin-bottom: 1.5rem; font-weight: 500;">Créez un compte pour suivre vos commandes et bénéficier d'avantages exclusifs.</p>
                    
                    <form action="{{ route('checkout.shipping') }}" method="POST">
                        @csrf
                        <input type="hidden" name="checkout_type" value="register">
                        
                        <div class="checkout-form-group">
                            <div class="checkout-form-grid">
                                <div>
                                    <label for="register_name" class="checkout-label">Nom complet</label>
                                    <input type="text" name="register_name" id="register_name" required class="checkout-input">
                                </div>
                                
                                <div>
                                    <label for="register_email" class="checkout-label">Email</label>
                                    <input type="email" name="register_email" id="register_email" required class="checkout-input">
                                </div>
                            </div>
                        </div>
                        
                        <div class="checkout-form-group">
                            <div class="checkout-form-grid">
                                <div>
                                    <label for="register_password" class="checkout-label">Mot de passe</label>
                                    <input type="password" name="register_password" id="register_password" required class="checkout-input">
                                </div>
                                
                                <div>
                                    <label for="register_password_confirmation" class="checkout-label">Confirmer le mot de passe</label>
                                    <input type="password" name="register_password_confirmation" id="register_password_confirmation" required class="checkout-input">
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" class="checkout-btn">
                            <i class="fas fa-user-plus"></i>
                            Créer mon compte et continuer
                        </button>
                    </form>
                </div>

                <!-- Se connecter -->
                <div class="checkout-section">
                    <h3 class="checkout-section-title">J'ai déjà un compte</h3>
                    <p style="color: var(--astro-text-secondary); margin-bottom: 1.5rem; font-weight: 500;">Connectez-vous pour accéder à vos informations sauvegardées.</p>
                    
                    <form action="{{ route('checkout.shipping') }}" method="POST">
                        @csrf
                        <input type="hidden" name="checkout_type" value="login">
                        
                        <div class="checkout-form-group">
                            <div class="checkout-form-grid">
                                <div>
                                    <label for="login_email" class="checkout-label">Email</label>
                                    <input type="email" name="login_email" id="login_email" required class="checkout-input">
                                </div>
                                
                                <div>
                                    <label for="login_password" class="checkout-label">Mot de passe</label>
                                    <input type="password" name="login_password" id="login_password" required class="checkout-input">
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" class="checkout-btn" style="background: rgba(59, 130, 246, 1); color: white;">
                            <i class="fas fa-sign-in-alt"></i>
                            Se connecter et continuer
                        </button>
                    </form>
                </div>

                <!-- Continuer en tant qu'invité -->
                <div class="checkout-section">
                    <h3 class="checkout-section-title">Continuer en tant qu'invité</h3>
                    <p style="color: var(--astro-text-secondary); margin-bottom: 1.5rem; font-weight: 500;">Passez votre commande sans créer de compte.</p>
                    
                    <form action="{{ route('checkout.shipping') }}" method="GET">
                        <input type="hidden" name="checkout_type" value="guest">
                        <button type="submit" class="checkout-btn secondary">
                            <i class="fas fa-user"></i>
                            Continuer en tant qu'invité
                        </button>
                    </form>
                </div>
            @endauth

        </div>
    </div>

    <!-- Navigation -->
    <div class="checkout-navigation">
        <a href="{{ route('cart.index') }}" class="checkout-btn secondary">
            <i class="fas fa-arrow-left"></i>
            Retour au panier
        </a>
    </div>
</div>
@endsection
