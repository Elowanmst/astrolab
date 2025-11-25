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
                @foreach($cartItems as $itemKey => $item)
                    <div class="cart-item">
                        <div class="item-image">
                            @if(!empty($item['image']))
                                <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div class="image-placeholder" style="display: none;">
                                    <i class="fas fa-image"></i>
                                    <span>IMG</span>
                                </div>
                            @else
                                <div class="image-placeholder">
                                    <i class="fas fa-image"></i>
                                    <span>IMG</span>
                                </div>
                            @endif
                        </div>
                        <div class="item-details">
                            <h3 class="item-name">| {{ mb_strtoupper($item['name'], 'UTF-8') }} |</h3>
                            @if(isset($item['size']) && $item['size'])
                                <p class="item-size">Taille: {{ $item['size'] }}</p>
                            @endif
                            @if(isset($item['color']) && $item['color'])
                                <p class="item-color">Couleur: {{ $item['color'] }}</p>
                            @endif
                            <p class="item-price">{{ number_format($item['price'], 2) }} €</p>
                        </div>
                        <div class="item-quantity">
                            <label>QUANTITÉ</label>
                            <div class="quantity-controls">
                                <button class="qty-btn minus" data-item-key="{{ $itemKey }}">-</button>
                                <span class="qty-value">{{ $item['quantity'] }}</span>
                                <button class="qty-btn plus" data-item-key="{{ $itemKey }}" data-stock-available="{{ $item['stock_available'] ?? 99 }}">+</button>
                            </div>
                        </div>
                        <div class="item-total">
                            <p class="total-label">TOTAL</p>
                            <p class="total-price">{{ number_format($item['price'] * $item['quantity'], 2) }} €</p>
                        </div>
                        <div class="item-remove">
                            <form action="{{ route('cart.remove', $itemKey) }}" method="POST" class="remove-form">
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
                        <span class="text-gray-400 text-sm">Calculée à l'étape suivante</span>
                    </div>
                    <div class="summary-line">
                        <span class="text-gray-400">TOTAL (hors livraison) :</span>
                        <span>{{ number_format($total, 2) }} €</span>
                    </div>
                    <div class="summary-actions">
                        <a href="{{ route('home') }}" class="btn-continue">
                            <i class="fas fa-arrow-left"></i> CONTINUER MES ACHATS
                        </a>
                        <a href="{{ route('checkout.index') }}" class="btn-checkout">
                            <i class="fas fa-credit-card"></i> COMMANDER
                        </a>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestion des boutons de quantité
    document.querySelectorAll('.qty-btn').forEach(button => {
        button.addEventListener('click', function() {
            const itemKey = this.dataset.itemKey;
            const isPlus = this.classList.contains('plus');
            const qtyValue = this.parentElement.querySelector('.qty-value');
            let currentQty = parseInt(qtyValue.textContent);
            
            if (isPlus) {
                const stockAvailable = parseInt(this.dataset.stockAvailable) || 99;
                if (currentQty >= stockAvailable) {
                    alert(`Stock insuffisant. Maximum disponible : ${stockAvailable}`);
                    return;
                }
                currentQty++;
            } else {
                currentQty--;
            }
            
            // Ne pas aller en dessous de 0
            if (currentQty < 0) currentQty = 0;
            
            updateQuantity(itemKey, currentQty);
        });
    });
    
    function updateQuantity(itemKey, quantity) {
        fetch('{{ route("cart.updateQuantity") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                item_key: itemKey,
                quantity: quantity
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Recharger la page pour mettre à jour les totaux
                location.reload();
            } else {
                alert(data.message || 'Erreur lors de la mise à jour de la quantité');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Erreur lors de la mise à jour de la quantité');
        });
    }
});
</script>
@endsection