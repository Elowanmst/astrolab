@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-[#222222] flex items-center justify-center px-4">
    <div class="text-center max-w-2xl">
        <!-- Icône d'erreur -->
        <div class="mb-8">
            <div class="w-24 h-24 bg-red-500 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </div>
        </div>
        
        <!-- Titre principal -->
        <h1 class="text-5xl font-bold text-white mb-6">PAIEMENT ÉCHOUÉ</h1>
        
        <!-- Message d'erreur -->
        <div class="space-y-4 mb-8">
            <p class="text-xl text-gray-300">
                Un problème est survenu lors du traitement de votre paiement.
            </p>
            @if(isset($error))
                <div class="bg-red-500 bg-opacity-10 border border-red-500 rounded-lg p-4">
                    <p class="text-red-400 text-sm">
                        {{ $error }}
                    </p>
                </div>
            @endif
            <p class="text-lg text-gray-400">
                Votre commande n'a pas été validée et aucun débit n'a eu lieu.
            </p>
            <p class="text-gray-500">
                Vous pouvez réessayer ou utiliser un autre moyen de paiement.
            </p>
        </div>
        
        <!-- Informations utiles -->
        <div class="bg-white bg-opacity-5 rounded-lg p-6 mb-8">
            <h3 class="text-xl font-bold text-white mb-4">QUE FAIRE ?</h3>
            <div class="space-y-3 text-gray-400 text-left">
                <p>✅ Vérifiez les informations de votre carte</p>
                <p>💳 Assurez-vous d'avoir suffisamment de fonds</p>
                <p>🏦 Contactez votre banque si le problème persiste</p>
                <p>📧 Essayez un autre moyen de paiement</p>
                <p>🔄 Tentez à nouveau dans quelques minutes</p>
            </div>
        </div>
        
        <!-- Boutons d'action -->
        <div class="space-y-4">
            <div class="flex justify-center space-x-4 mb-6">
                <a href="{{ route('cart.show') }}" class="btn-primary px-8 py-4 text-lg">
                    RÉESSAYER LE PAIEMENT
                </a>
                <a href="{{ route('home') }}" class="btn-secondary px-6 py-3">
                    RETOUR À L'ACCUEIL
                </a>
            </div>
            
            <div class="flex justify-center space-x-4">
                <a href="{{ url('/#boutique') }}" class="btn-secondary px-6 py-3">
                    CONTINUER MES ACHATS
                </a>
            </div>
        </div>
        
        <!-- Support -->
        <div class="mt-12 text-sm text-gray-500">
            <p>Problème persistant ? Contactez notre support :</p>
            <p><a href="mailto:support@astrolab-boutique.com" class="text-gray-400 hover:text-white">support@astrolab-boutique.com</a></p>
            <p class="mt-2 text-xs">Joignez les détails de l'erreur pour un traitement plus rapide</p>
        </div>
    </div>
</div>
@endsection
