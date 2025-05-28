@extends('layouts.app')

@section('title', 'Astrolab - ' . $product->name)

@section('content')

<div class="product-container">
    
    <a href="{{ route('home') }}#service" class="btn">Retour</a>
    
    {{-- <div class="carousel-container-show">
        <button class="prev-show">❮</button>
        <div class="carousel-show">
            @foreach ($product->getMedia('products') as $media)
            <img src="{{ $media->getUrl('carousel') }}" alt="{{ $product->name }}" class="product-image-show">
            @endforeach
        </div>
        <button class="next-show">❯</button>
    </div> --}}

    <div class="gallery">
        @foreach ($product->getMedia('products') as $media)
        <img class="product-image" 
            src="{{ $product->getFirstMediaUrl('products', 'preview') ?: asset('default-image.jpg') }}" 
            alt="Image de {{ $product->name }}">
        @endforeach
    </div>
    
    
    
    
    <h1>| {{ $product->name }} |</h1>
    <h3>{{ $product->price }}€</h3>
    <p>Taxes incluses</p>
    
    <div class="size-select">
        <label for="size">Taille</label>
        <div class="size-options">
            <button type="button" class="size-option" data-size="S">S</button>
            <button type="button" class="size-option" data-size="M">M</button>
            <button type="button" class="size-option" data-size="L">L</button>
            <button type="button" class="size-option" data-size="XL">XL</button>
        </div>
        <input type="hidden" name="size" id="size" value=""> <!-- Champ caché pour stocker la taille sélectionnée -->
    </div>    

    <form class="" action="{{ route('home') }}" method="POST">
        @csrf
    
        <input type="hidden" name="product_id" value="{{ $product->id }}">
    
        <label for="color">Choisir une couleur :</label>
        <select name="color" id="color" required>
            @foreach($product->colors as $color)
                <option value="{{ $color->id }}">{{ $color->name }}</option>
            @endforeach
        </select>

        
        <div class="gallery">
            @foreach ($product->getMedia('products') as $media)
            <img src="{{ $media->getUrl('thumb') }}" alt="{{ $product->name }}" style="max-width: 200px; border-radius: 8px;">
            @endforeach
        </div>
    
        <label for="quantity">Quantité :</label>
        <input type="number" name="quantity" id="quantity" min="1" value="1">
    
        <button type="submit">Ajouter au panier</button>
    </form>
    
    
    
</div>



<button id="openModal" class="btn-primary">Contactez-nous</button>

<footer>
    <p>© 2025 - ASTROLAB  </p>
    <br>
    <p>created by ec-craft.fr  </p>
</footer>

@endsection