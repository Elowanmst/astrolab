@extends('layouts.app')
@inject('cartService', 'App\Services\Cart')

@section('content')
@php
    $cart = $cartService->get();
    $subtotal = 0;
    foreach($cart as $item) {
        $subtotal += $item['price'] * $item['quantity'];
    }

    // Calcul des frais de port (logique identique au contrôleur)
    $shippingMethod = session('checkout_data.shipping_method', 'home');
    $shippingCost = $cartService->getShippingCost($shippingMethod);
    
    // Fallback si le prix est 0 (comme dans le contrôleur)
    if ($shippingCost == 0) {
        if ($shippingMethod === 'home') {
            $shippingCost = 6.99;
        } elseif ($shippingMethod === 'pickup') {
            $shippingCost = 4.99;
        }
    }

    $total = $subtotal + $shippingCost;
@endphp

<div class="min-h-screen pt-40 pb-12 flex flex-col items-center justify-start px-4">
    <div class="max-w-2xl w-full">
        <h1 class="text-4xl font-bebas tracking-widest text-white mb-12 uppercase text-center border-b border-white/20 pb-6">Récapitulatif de commande</h1>

        @if(count($cart) > 0)
            <div class="space-y-6 mb-12">
                @foreach($cart as $item)
                    <div class="flex items-center justify-between pb-6 border-b border-white/10 last:border-0">
                        <div class="flex items-center gap-6">
                            @if(isset($item['image']))
                                <div class="w-20 h-20 flex-shrink-0 rounded overflow-hidden border border-white/20">
                                    <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="w-full h-full object-cover">
                                </div>
                            @endif
                            <div>
                                <h3 class="text-2xl font-bebas tracking-wide text-white mb-1">{{ $item['name'] }}</h3>
                                <p class="text-gray-300 font-light">
                                    Taille: <span class="font-medium">{{ $item['size'] }}</span>
                                    @if(isset($item['color']) && $item['color']) 
                                        <span class="mx-2">|</span> Couleur: <span class="font-medium">{{ $item['color'] }}</span> 
                                    @endif
                                </p>
                                <p class="text-gray-400 text-sm mt-1">Quantité: {{ $item['quantity'] }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-2xl font-bold text-white">{{ number_format($item['price'] * $item['quantity'], 2, ',', ' ') }} €</p>
                            @if($item['quantity'] > 1)
                                <p class="text-sm text-gray-400">{{ number_format($item['price'], 2, ',', ' ') }} € / unité</p>
                            @endif
                        </div>
                    </div>
                @endforeach

                {{-- Frais de livraison --}}
                <div class="flex items-center justify-between pb-6 border-b border-white/10">
                    <div class="flex items-center gap-6">
                        <div class="w-20 h-20 flex-shrink-0 flex items-center justify-center rounded border border-white/20 bg-white/5">
                            <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bebas tracking-wide text-white mb-1">Livraison</h3>
                            <p class="text-gray-300 font-light">
                                @if($shippingMethod === 'pickup')
                                    Point Relais
                                @else
                                    À domicile
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-2xl font-bold text-white">{{ number_format($shippingCost, 2, ',', ' ') }} €</p>
                    </div>
                </div>
            </div>

            <div class="flex justify-between items-end border-t border-white pt-6 mb-10">
                <span class="text-2xl font-bebas tracking-widest text-gray-300">Total à payer</span>
                <span class="text-5xl font-bebas tracking-wider text-white">{{ number_format($total, 2, ',', ' ') }} €</span>
            </div>

            <form action="{{ route('stripe.process') }}" method="POST" class="flex justify-center">
                @csrf
                <button type="submit" class="group relative inline-flex items-center justify-center px-8 py-4 text-lg font-bold text-gray-900 uppercase tracking-widest bg-white rounded hover:bg-gray-100 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white shadow-[0_0_20px_rgba(255,255,255,0.3)] hover:shadow-[0_0_30px_rgba(255,255,255,0.5)]">
                    <span class="mr-3">Payer par Carte Bancaire</span>
                    <svg class="w-6 h-6 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                    </svg>
                </button>
            </form>
        @else
            <div class="text-center py-12 border border-white/10 rounded-lg bg-white/5 backdrop-blur-sm">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
                <p class="text-white text-xl mb-8 font-light">Votre panier est vide.</p>
                <a href="{{ url('/') }}" class="inline-block border border-white text-white font-bold py-3 px-8 rounded hover:bg-white hover:text-gray-900 transition-colors duration-300 uppercase tracking-wider">
                    Retour à la boutique
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
