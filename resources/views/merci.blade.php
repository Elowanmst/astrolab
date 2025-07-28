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
        <h1 class="text-5xl font-bold text-white mb-6">MERCI !</h1>
        
        <!-- Message de confirmation -->
        <div class="space-y-4 mb-8">
            <p class="text-xl text-gray-300">
                Votre message a été envoyé avec succès.
            </p>
            <p class="text-lg text-gray-400">
                Nous avons bien reçu votre demande et nous vous répondrons dans les plus brefs délais.
            </p>
            <p class="text-gray-500">
                En général, nous répondons dans les 24 heures.
            </p>
        </div>
        
        <!-- Informations supplémentaires -->
        <div class="bg-white bg-opacity-5 rounded-lg p-6 mb-8">
            <h3 class="text-xl font-bold text-white mb-4">EN ATTENDANT...</h3>
            <div class="space-y-3 text-gray-400">
                <p>📧 Vérifiez votre boîte email pour une confirmation</p>
                <p>📱 Suivez-nous sur Instagram : @astrolab_official</p>
                <p>⏰ Découvrez nos précommandes en cours</p>
            </div>
        </div>
        
        <!-- Boutons d'action -->
        <div class="space-y-4">
            <a href="{{ url('/') }}" class="btn-primary inline-block px-8 py-4 text-lg">
                RETOUR À L'ACCUEIL
            </a>
            <div class="flex justify-center space-x-4">
                <a href="{{ url('/#boutique') }}" class="btn-secondary px-6 py-3">
                    VOIR LA BOUTIQUE
                </a>
                <a href="https://instagram.com/" target="_blank" class="btn-secondary px-6 py-3">
                    INSTAGRAM
                </a>
            </div>
        </div>
        
        <!-- Note personnelle -->
        <div class="mt-8 p-4 bg-white bg-opacity-5 rounded-lg">
            <p class="text-sm text-gray-500">
                Équipe ASTROLAB - Créations exclusives depuis 2025
            </p>
        </div>
    </div>
</div>
@endsection