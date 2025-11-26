@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-[#222222] flex items-center justify-center px-4">
    <div class="text-center max-w-2xl">
        <!-- Icône de succès -->
        <div class="mb-8">
            <div class="w-24 h-24 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
        </div>
        
        <!-- Titre principal -->
        <h1 class="text-5xl font-bold text-white mb-6">PAIEMENT CONFIRMÉ !</h1>
        
        <!-- Message de confirmation -->
        <div class="space-y-4 mb-8">
            <p class="text-xl text-gray-300">
                Votre commande a été traitée avec succès.
            </p>
            @if(isset($order))
                <p class="text-lg text-gray-400">
                    Commande <span class="text-white font-bold">#{{ $order->order_number }}</span>
                </p>
                <p class="text-lg text-gray-400">
                    Montant total : <span class="text-white font-bold">{{ number_format($order->total_amount, 2) }}€</span>
                </p>
            @endif
            <p class="text-gray-500">
                Un email de confirmation vous a été envoyé avec tous les détails de votre commande.
            </p>
        </div>
        
        <!-- Informations supplémentaires -->
        <div class="bg-white bg-opacity-5 rounded-lg p-6 mb-8">
            <h3 class="text-xl font-bold text-white mb-4">PROCHAINES ÉTAPES</h3>
            <div class="space-y-3 text-gray-400">
                <p>📦 Préparation de votre commande dans les 24-48h</p>
                <p>📧 Vous recevrez un numéro de suivi par email</p>
                @if(isset($order) && $order->shipping_method === 'pickup')
                    <p>� Livraison en point relais sous 3-5 jours ouvrés</p>
                @else
                    <p>�🚚 Livraison à domicile sous 3-5 jours ouvrés</p>
                @endif
            </div>
        </div>

        @if(isset($order) && $order->shipping_method === 'pickup' && $order->relay_point_name)
        <!-- Informations point relais -->
        <div class="bg-blue-500 bg-opacity-10 border border-blue-500 rounded-lg p-6 mb-8">
            <h3 class="text-xl font-bold text-blue-400 mb-4">📍 VOTRE POINT RELAIS</h3>
            <div class="text-left space-y-2 text-gray-300">
                <p class="text-white font-bold">{{ $order->relay_point_name }}</p>
                @if($order->relay_point_address)
                    <p>{{ $order->relay_point_address }}</p>
                @endif
                @if($order->relay_point_postal_code && $order->relay_point_city)
                    <p>{{ $order->relay_point_postal_code }} {{ $order->relay_point_city }}</p>
                @endif
                @if($order->relay_point_id)
                    <p class="text-sm text-gray-400">Code point relais : {{ $order->relay_point_id }}</p>
                @endif
            </div>
            <div class="mt-4 text-sm text-blue-300">
                <p>💡 Vous recevrez un SMS/email lorsque votre colis sera disponible en point relais</p>
            </div>
        </div>
        @endif
        
        <!-- Boutons d'action -->
        <div class="space-y-4">
            <a href="{{ route('home') }}" class="btn-primary inline-block px-8 py-4 text-lg">
                RETOUR À L'ACCUEIL
            </a>
            <div class="flex justify-center space-x-4">
                <a href="{{ url('/#boutique') }}" class="btn-secondary px-6 py-3">
                    CONTINUER MES ACHATS
                </a>
                <a href="https://instagram.com/" target="_blank" class="btn-secondary px-6 py-3">
                    NOUS SUIVRE
                </a>
            </div>
        </div>
        
        <!-- Support -->
        <div class="mt-12 text-sm text-gray-500">
            <p>Besoin d'aide ? Contactez-nous : <a href="mailto:support@astrolab-boutique.com" class="text-gray-400 hover:text-white">support@astrolab-boutique.com</a></p>
        </div>
    </div>
</div>
@endsection
