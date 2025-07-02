@extends('layouts.app')

@section('content')
<div class="cart-container">
    <div class="cart-header">
        <h1><i class="fas fa-shopping-cart"></i> MON PANIER</h1>
        <div class="cart-breadcrumb">
            <a href="{{ route('home') }}">ACCUEIL</a> / <span>PANIER</span>
        </div>
    </div>

    @if(count($cartItems) > 0)
        <div class="cart-content">
            <div class="cart-items">
                @foreach($cartItems as $item)
                    <div class="cart-item">
                        <div class="item-image">
                            <img src="{{ asset('storage/' . ($item['image'] ?? 'default-image.jpg')) }}" alt="{{ $item['name'] }}">
                        </div>
                        <div class="item-details">
                            <h3 class="item-name">| {{ mb_strtoupper($item['name'], 'UTF-8') }} |</h3>
                            <p class="item-price">{{ number_format($item['price'], 2) }} €</p>
                        </div>
                        <div class="item-quantity">
                            <label>QUANTITÉ</label>
                            <div class="quantity-controls">
                                <button class="qty-btn minus" data-id="{{ $item['product_id'] }}">-</button>
                                <span class="qty-value">{{ $item['quantity'] }}</span>
                                <button class="qty-btn plus" data-id="{{ $item['product_id'] }}">+</button>
                            </div>
                        </div>
                        <div class="item-total">
                            <p class="total-label">TOTAL</p>
                            <p class="total-price">{{ number_format($item['price'] * $item['quantity'], 2) }} €</p>
                        </div>
                        <div class="item-remove">
                            <form action="{{ route('cart.remove', $item['product_id']) }}" method="POST" class="remove-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="remove-btn" title="Supprimer">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="cart-summary">
                <div class="summary-box">
                    <h3>RÉCAPITULATIF</h3>
                    <div class="summary-line">
                        <span>Sous-total :</span>
                        <span>{{ number_format($total, 2) }} €</span>
                    </div>
                    <div class="summary-line">
                        <span>Livraison :</span>
                        <span>GRATUITE</span>
                    </div>
                    <div class="summary-line total-line">
                        <span>TOTAL :</span>
                        <span>{{ number_format($total, 2) }} €</span>
                    </div>
                    <div class="summary-actions">
                        <a href="{{ route('home') }}" class="btn-continue">
                            <i class="fas fa-arrow-left"></i> CONTINUER MES ACHATS
                        </a>
                        <button class="btn-checkout">
                            <i class="fas fa-credit-card"></i> COMMANDER
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="empty-cart">
            <i class="fas fa-shopping-cart empty-icon"></i>
            <h2>VOTRE PANIER EST VIDE</h2>
            <p>Découvrez nos éditions éphémères exclusives</p>
            <a href="{{ route('home') }}" class="btn-shop">
                <i class="fas fa-search"></i> DÉCOUVRIR NOS PRODUITS
            </a>
        </div>
    @endif
</div>
@endsection