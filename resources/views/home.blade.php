@extends('layouts.app')

@section('content')
    <a href="/products/create">créer un produit</a>
    <a href="/products">voir les produits</a>
    <div class="text-white">

        <div class="bg-gradient-to-b from-black to-grey-900 w-full h-[400px] text-center pt-20">

            <h1 class="text-9xl mb-[100px] ">ASTROLAB</h1>
            <div class="mb-[100px] font-bebas">
                <h2>ASTROLAB</h2>
                <p class="text-gray-400">| DES EDITIONS ÉPHÉMÈRES EXCLUSIVES IMAGINÉES PAR DES ILLUSTRATEURS INDÉPENDANTS |</p>
            </div>

            <div class="w-[1300px] h-1 bg-white shadow-[0_0_10px_2px_rgba(255,255,255,0.7)] mx-auto pt-"></div>

            <div class="flex flex-col mt-[100px]">
                <h2 class="text-4xl">BOUTIQUE</h2>
                <br>
                <p class="text-gray-400">FIN DES PRÉCOMMANDES DANS...</p>
                <p class="text-gray-400">| JOUR(S) - HEURE(S) - MINUTE(S) - SECONDE(S) |</p>

                <div class="flex justify-center mt-[80px]">
                    <button class="bg-white text-black px-10 py-2 rounded-full">PRÉCOMMANDEZ</button>
                </div>
            </div>

            {{-- <div id="products">
                <ul class="list-none p-0 m-0 mt-4 space-y-4 bg-slate-100 rounded-full">
                    @foreach ($products as $product)
                        <h2 class="text-xl" >
                            <a href="{{ route('product.show', $article) }}">{{$article->title}}</a>
                        </h2>
                    @endforeach
                </ul>
            </div> --}}
        </div>
    </div>
@endsection