@extends('layouts.app')

@section('title', 'Astrolab - ' . $product->name)

@section('content')

<div class="product-detail-container">
    <!-- Navigation breadcrumb -->
    <div class="product-breadcrumb">
        <a href="{{ route('home') }}" class="breadcrumb-link">
            <i class="fas fa-home"></i> Accueil
        </a>
        <span class="breadcrumb-separator">></span>
        <a href="{{ route('home') }}#boutique" class="breadcrumb-link">Boutique</a>
        <span class="breadcrumb-separator">></span>
        <span class="breadcrumb-current">{{ $product->name }}</span>
    </div>
    
    <div class="product-detail-grid">
        <!-- Galerie d'images -->
        <div class="product-gallery-section">
            <div class="main-image-container">
                @if($product->getMedia('products')->count() > 0)
                @foreach ($product->getMedia('products') as $index => $media)
                <img 
                src="{{ $media->getUrl() }}" 
                alt="{{ $product->name }} - Image {{ $index + 1 }}"
                class="main-product-image {{ $index === 0 ? 'active' : '' }}"
                data-image-index="{{ $index }}"
                >
                @endforeach
                @else
                <div class="no-image-placeholder">
                    <i class="fas fa-image"></i>
                    <p>Image bientôt disponible</p>
                </div>
                @endif
                
                <!-- Badges sur l'image -->
                <div class="product-badges">
                    @if($product->stock < 5 && $product->stock > 0)
                    <span class="badge badge-low-stock">Dernières pièces</span>
                    @elseif($product->stock === 0)
                    <span class="badge badge-out-of-stock">Rupture de stock</span>
                    @endif
                </div>
            </div>
            
            <!-- Miniatures -->
            @if($product->getMedia('products')->count() > 1)
            <div class="thumbnail-gallery">
                @foreach ($product->getMedia('products') as $index => $media)
                <div class="thumbnail-item {{ $index === 0 ? 'active' : '' }}" data-image-index="{{ $index }}">
                    <img 
                    src="{{ $media->getUrl() }}" 
                    alt="{{ $product->name }} - Miniature {{ $index + 1 }}"
                    >
                </div>
                @endforeach
            </div>
            @endif
        </div>
        
        <!-- Informations produit -->
        <div class="product-info-section">
            <!-- En-tête produit -->
            <div class="product-header">
                <h1 class="product-title">| {{ strtoupper($product->name) }} |</h1>
                @if($product->category)
                <span class="product-category">{{ $product->category->name }}</span>
                @endif
                
                <div class="product-price-container">
                    <span class="product-price">{{ $product->price }}€</span>
                    <span class="price-tax-info">TTC</span>
                </div>
            </div>
            
            <!-- Description -->
            @if($product->description)
            <div class="product-description">
                <h3>Description</h3>
                {!! \Illuminate\Support\Str::markdown($product->description) !!}
            </div>
            @endif
            
            
            <!-- Informations techniques -->
            {{-- <div class="product-specs">
                @if($product->material)
                <div class="spec-item">
                    <span class="spec-label">Matière :</span>
                    <span class="spec-value">{{ $product->material }}</span>
                </div>
                @endif
                @if($product->gender)
                <div class="spec-item">
                    <span class="spec-label">Genre :</span>
                    <span class="spec-value">{{ $product->gender }}</span>
                </div>
                @endif
            </div> --}}
            
            <!-- Formulaire d'ajout au panier -->
            <form action="{{ route('cart.add', ['product' => $product->id]) }}" method="POST" class="add-to-cart-form">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                
                <!-- Sélection des couleurs -->
                @if($product->colors->count() > 0)
                <div class="product-option-group">
                    <label class="option-label">Couleur</label>
                    <div class="color-selector">
                        @foreach($product->colors as $color)
                        <div class="color-option-container">
                            <input 
                            type="radio" 
                            name="color" 
                            value="{{ $color->id }}" 
                            id="color-{{ $color->id }}"
                            class="color-input"
                            {{ $loop->first ? 'checked' : '' }}
                            >
                            <label for="color-{{ $color->id }}" class="color-option" 
                                style="background-color: {{ $color->hex ?? '#cccccc' }};"
                                title="{{ $color->name }}">
                                <span class="color-name">{{ $color->name }}</span>
                            </label>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
                
                <!-- Sélection de la taille -->
                <div class="product-option-group">
                    <label class="option-label">Taille</label>
                    <div class="size-selector">
                        <button type="button" class="size-option" data-size="XXS">XXS</button>
                        <button type="button" class="size-option" data-size="XS">XS</button>
                        <button type="button" class="size-option" data-size="S">S</button>
                        <button type="button" class="size-option" data-size="M">M</button>
                        <button type="button" class="size-option" data-size="L">L</button>
                        <button type="button" class="size-option" data-size="XL">XL</button>
                    </div>
                    <input type="hidden" name="size" id="selected-size">
                </div>
                
                <!-- Guide des tailles -->
                <div class="size-guide-link">
                    <a href="#" onclick="openSizeGuide()" class="guide-link">
                        <i class="fas fa-ruler"></i> Guide des tailles
                    </a>
                </div>
                
                <!-- Quantité -->
                <div class="product-option-group">
                    <label for="quantity" class="option-label">Quantité</label>
                    <div class="quantity-selector">
                        <button type="button" class="qty qty-minus" onclick="changeQuantity(-1)">-</button>
                        <input type="number" name="quantity" id="quantity" min="1" max="{{ $product->stock ?? 99 }}" value="1" class="qty-input">
                        <button type="button" class="qty qty-plus" onclick="changeQuantity(1)">+</button>
                    </div>
                    @if($product->stock ?? 0 > 0)
                    <small class="stock-info">{{ $product->stock ?? 'Plusieurs' }} en stock</small>
                    @endif
                </div>
                
                <!-- Boutons d'action -->
                <div class="product-actions">
                    <button type="submit" class="btn-add-to-cart" {{ ($product->stock ?? 1) === 0 ? 'disabled' : '' }}>
                        <i class="fas fa-shopping-bag"></i>
                        {{ ($product->stock ?? 1) === 0 ? 'Rupture de stock' : 'Ajouter au panier' }}
                    </button>
                    
                    {{-- <div class="secondary-actions">
                        <button type="button" class="btn-wishlist" onclick="addToWishlist({{ $product->id }})">
                            <i class="far fa-heart"></i>
                            Ajouter aux favoris
                        </button>
                        <button type="button" class="btn-share" onclick="shareProduct()">
                            <i class="fas fa-share-alt"></i>
                            Partager
                        </button>
                    </div> --}}
                </div>
            </form>
            
            <!-- Informations de livraison -->
            <div class="shipping-info">
                <div class="shipping-item">
                    <i class="fas fa-truck"></i>
                    <span>Livraison en France métropolitaine</span>
                </div>
                <div class="shipping-item">
                    <i class="fas fa-undo"></i>
                    <span>Retours gratuits sous 30 jours</span>
                </div>
                <div class="shipping-item">
                    <i class="fas fa-shield-alt"></i>
                    <span>Paiement sécurisé</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal guide des tailles -->
<div id="sizeGuideModal" class="size-guide-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Guide des tailles</h3>
            <button class="modal-close" onclick="closeSizeGuide()">&times;</button>
        </div>
        <div class="modal-body">
            <table class="size-table">
                <thead>
                    <tr>
                        <th>Taille</th>
                        <th>Tour de poitrine (cm)</th>
                        <th>Tour de taille (cm)</th>
                        <th>Longueur (cm)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>XXS</td>
                        <td>82-86</td>
                        <td>62-66</td>
                        <td>66</td>
                    </tr>
                    <tr>
                        <td>XS</td>
                        <td>86-90</td>
                        <td>66-70</td>
                        <td>68</td>
                    </tr>
                    <tr>
                        <td>S</td>
                        <td>90-94</td>
                        <td>70-74</td>
                        <td>70</td>
                    </tr>
                    <tr>
                        <td>M</td>
                        <td>94-98</td>
                        <td>74-78</td>
                        <td>72</td>
                    </tr>
                    <tr>
                        <td>L</td>
                        <td>98-102</td>
                        <td>78-82</td>
                        <td>74</td>
                    </tr>
                    <tr>
                        <td>XL</td>
                        <td>102-106</td>
                        <td>82-86</td>
                        <td>76</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>



<script>
    // Fallback au cas où le script principal ne se charge pas à temps
    if (typeof changeQuantity === 'undefined') {
        function changeQuantity(change) {
            const quantityInput = document.getElementById('quantity');
            const currentValue = parseInt(quantityInput.value);
            const minValue = parseInt(quantityInput.min) || 1;
            const maxValue = parseInt(quantityInput.max) || 999;
            
            const newValue = currentValue + change;
            
            if (newValue >= minValue && newValue <= maxValue) {
                quantityInput.value = newValue;
            }
        }
    }
    
    // Fallback pour les autres fonctions
    if (typeof openSizeGuide === 'undefined') {
        function openSizeGuide() {
            const modal = document.getElementById('sizeGuideModal');
            if (modal) {
                modal.style.display = 'flex';
            }
        }
    }
    
    if (typeof closeSizeGuide === 'undefined') {
        function closeSizeGuide() {
            const modal = document.getElementById('sizeGuideModal');
            if (modal) {
                modal.style.display = 'none';
            }
        }
    }
    
    // Amélioration du contraste pour les couleurs claires
    document.addEventListener('DOMContentLoaded', function() {
        // Fonction pour détecter si une couleur est claire
        function isLightColor(color) {
            // Convertir la couleur hex en RGB
            let r, g, b;
            
            if (color.startsWith('#')) {
                const hex = color.slice(1);
                r = parseInt(hex.substr(0, 2), 16);
                g = parseInt(hex.substr(2, 2), 16);
                b = parseInt(hex.substr(4, 2), 16);
            } else if (color.startsWith('rgb')) {
                const matches = color.match(/\d+/g);
                r = parseInt(matches[0]);
                g = parseInt(matches[1]);
                b = parseInt(matches[2]);
            } else {
                return false;
            }
            
            // Calculer la luminance
            const luminance = (0.299 * r + 0.587 * g + 0.114 * b) / 255;
            return luminance > 0.8; // Seuil pour déterminer si c'est clair
        }
        
        // Appliquer des styles spéciaux aux couleurs claires
        const colorOptions = document.querySelectorAll('.color-option');
        colorOptions.forEach(option => {
            const bgColor = option.style.backgroundColor;
            if (bgColor && isLightColor(bgColor)) {
                option.classList.add('light-color');
            }
        });
    });
    
    // CSS dynamique pour les couleurs claires
    const style = document.createElement('style');
    style.textContent = `
    .color-option.light-color {
        border: 3px solid #d0d0d0 !important;
    }
    
    .color-input:checked + .color-option.light-color::after {
        background: rgba(0, 0, 0, 0.95) !important;
        border: 2px solid #222 !important;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.5), 0 0 0 1px rgba(0, 0, 0, 0.4) !important;
    }
    
    .color-option.light-color:hover {
        border-color: #999 !important;
    }
`;
    document.head.appendChild(style);
</script>

@endsection
