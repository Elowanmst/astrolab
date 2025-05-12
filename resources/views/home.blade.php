@extends('layouts.app')

@section('content')
<div class="">
    
    
    
    
    <section class="bg-home">
        <div class="">

        </div>
    </section>
    
    <div class=" w-full h-[400px] text-center ">
        
        
        
        <div class="home-title">
            <h1>ASTROLAB</h1>
            <p class="text-gray-400">| DES EDITIONS ÉPHÉMÈRES EXCLUSIVES IMAGINÉES PAR DES ILLUSTRATEURS INDÉPENDANTS |</p>
        </div>
        
        <div class="w-[1300px] h-1 bg-white shadow-[0_0_10px_2px_rgba(255,255,255,0.7)] mx-auto pt-"></div>
        
        <div class="flex flex-col mt-[100px]">
            <h2 class="text-4xl">BOUTIQUE</h2>
            <br>
            <p class="text-gray-400">FIN DES PRÉCOMMANDES DANS...</p>
            <p class="text-gray-400">| JOUR(S) - HEURE(S) - MINUTE(S) - SECONDE(S) |</p>
            
            <div class="flex justify-center mt-[80px]">
                <a class="btn-home">PRÉCOMMANDEZ</a>
            </div>
        </div>
        <div id="products">
            <ul>
                @foreach ($products as $product)
                <li class="product-item" onclick="window.location='{{ route('products.show', $product) }}'">
                    <img class="product-image" src="{{ asset('storage/' . $product->image) }}" alt="Image de {{ $product->name }}">
                    <h2>
                        <a href="{{ route('products.show', $product) }}">
                            | {{ mb_strtoupper($product->name, 'UTF-8') }} |
                        </a>
                    </h2>
                    <p>{{ $product->price }} €</p>
                </li>
                @endforeach
            </ul>
        </div>
        
        <div class="flex justify-center flex-col mt-[100px]">
            <h2 class="text-3xl">ILLUSTRATEURS - GRAPHISTES</h2>
            <h3 class="text-xl">| SUIVEZ MATHIS DOUILLARD & AXEL CHAPET SUR LEURS RÉSEAUX POUR DÉCOUVRIR LEUR UNIVERS |</h3>
            
            <div class="flex justify-center mt-[80px]">
                <a class="btn-home">MATHIS DOUILLARD</a>
                <a class="btn-home">AXEL CHAPET</a>
            </div>
        </div>
        
        <div class="flex justify-center flex-col mt-[100px]">
            <h2 class="text-3xl">CONTACT</h2>
            <h3 class="text-xl">| GILDAS CHAUVEL - FONDATEUR D'ASTROLAB |</h3>
            
            <p class="text-gray-400">06 00 00 00 00</p>
            <p class="text-gray-400">GILDAS@ASTROLAB.FR</p>
            
            <img src="" alt="icône Instagram">
        </div>
    </div>
</div>

@endsection