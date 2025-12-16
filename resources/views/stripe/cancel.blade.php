@extends('layouts.app')

@section('content')
<div class="min-h-screen pt-40 pb-12 flex flex-col items-center justify-start px-4">
    <div class="max-w-md w-full border border-white p-8 rounded-lg shadow-2xl text-center relative overflow-hidden">
        {{-- Effet de fond subtil --}}
        <div class="absolute top-0 left-0 w-full h-1 bg-white opacity-50"></div>

        <div class="mb-6 flex justify-center">
            <div class="rounded-full bg-white/20 p-4 backdrop-blur-sm">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </div>
        </div>
        
        <h1 class="text-4xl font-bebas tracking-widest text-white mb-4 uppercase">Paiement Annulé</h1>
        
        <p class="text-gray-100 mb-8 text-lg font-light">
            Vous avez annulé le processus de paiement.<br>Aucun débit n'a été effectué.
        </p>
        
        <div class="flex flex-col gap-4">
            <a href="{{ route('stripe.checkout') }}" class="inline-block bg-white text-gray-900 font-bold py-3 px-6 rounded hover:bg-gray-200 transition-colors duration-300 uppercase tracking-wider shadow-lg">
                Réessayer le paiement
            </a>
            
            <a href="{{ url('/') }}" class="text-gray-300 hover:text-white transition-colors duration-300 text-sm uppercase tracking-wide mt-2">
                Retour à l'accueil
            </a>
        </div>
    </div>
</div>
@endsection
