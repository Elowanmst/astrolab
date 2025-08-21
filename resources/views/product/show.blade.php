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
                    <p>{{ $product->description }}</p>
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
                        <button type="button" class="size-option" data-size="XS">XS</button>
                        <button type="button" class="size-option" data-size="S">S</button>
                        <button type="button" class="size-option" data-size="M">M</button>
                        <button type="button" class="size-option" data-size="L">L</button>
                        <button type="button" class="size-option" data-size="XL">XL</button>
                        <button type="button" class="size-option" data-size="XXL">XXL</button>
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
                    <span>Livraison gratuite en France métropolitaine</span>
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
                        <td>XS</td>
                        <td>88-92</td>
                        <td>68-72</td>
                        <td>68</td>
                    </tr>
                    <tr>
                        <td>S</td>
                        <td>92-96</td>
                        <td>72-76</td>
                        <td>70</td>
                    </tr>
                    <tr>
                        <td>M</td>
                        <td>96-100</td>
                        <td>76-80</td>
                        <td>72</td>
                    </tr>
                    <tr>
                        <td>L</td>
                        <td>100-104</td>
                        <td>80-84</td>
                        <td>74</td>
                    </tr>
                    <tr>
                        <td>XL</td>
                        <td>104-108</td>
                        <td>84-88</td>
                        <td>76</td>
                    </tr>
                    <tr>
                        <td>XXL</td>
                        <td>108-112</td>
                        <td>88-92</td>
                        <td>78</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<button id="openModal" class="btn-primary">Contactez-nous</button>

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
</script>

@endsection
