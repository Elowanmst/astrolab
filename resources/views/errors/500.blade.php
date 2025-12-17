@extends('layouts.app')

@section('title', 'Erreur serveur - Astrolab')

@section('content')
<div class="min-h-screen bg-[#222222] flex items-center justify-center px-4">
    <div class="text-center max-w-2xl">
        <!-- Icône d'erreur -->
        <div class="mb-8">
            <div class="w-32 h-32 bg-grey-600 rounded-full flex items-center justify-center mx-auto mb-6 border-4 border-white error-icon">
                <i class="fas fa-exclamation-triangle text-4xl text-white"></i>
            </div>
        </div>
        
        <!-- Titre principal -->
        <h1 class="text-5xl font-bold text-white mb-6">ASTROLAB</h1>
        <h2 class="text-3xl font-bold text-white mb-6">ERREUR SERVEUR</h2>
        
        <!-- Message d'erreur -->
        <div class="space-y-4 mb-8">
            <p class="text-xl text-gray-300">
                Houston, nous avons un problème technique...
            </p>
            <p class="text-lg text-gray-400">
                Nos ingénieurs travaillent pour résoudre le problème rapidement.
            </p>
        </div>
        
        <!-- Boutons d'action -->
        <div class="space-y-4">
            <button onclick="window.location.reload()" class="error-btn-primary inline-block px-8 py-4 text-lg">
                <i class="fas fa-redo mr-2"></i>
                RÉESSAYER
            </button>
            <div class="flex justify-center space-x-4">
                <a href="{{ route('home') }}" class="error-btn-secondary px-6 py-3">
                    <i class="fas fa-home mr-2"></i>
                    RETOUR À L'ACCUEIL
                </a>
                <a href="mailto:support@astrolab-boutique.com" class="error-btn-secondary px-6 py-3">
                    <i class="fas fa-envelope mr-2"></i>
                    SIGNALER LE PROBLÈME
                </a>
            </div>
        </div>
        
        <!-- Informations techniques (uniquement en mode debug) -->
        @if(config('app.debug') && isset($exception))
            <div class="mt-12 p-4 bg-red-900 bg-opacity-20 rounded-lg text-left">
                <p class="text-sm text-red-300 mb-2">
                    <strong>Erreur :</strong> {{ $exception->getMessage() }}
                </p>
                <p class="text-xs text-red-400">
                    <strong>Fichier :</strong> {{ $exception->getFile() }}:{{ $exception->getLine() }}
                </p>
            </div>
        @endif
        
        <!-- Note personnelle -->
        <div class="mt-8 p-4 bg-white bg-opacity-5 rounded-lg">
            <p class="text-sm text-gray-400">
                <i class="fas fa-tools mr-2"></i>
                Si le problème persiste, contactez notre support technique
            </p>
        </div>
    </div>
</div>

<!-- Animation CSS -->
<style>
@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-5px); }
    75% { transform: translateX(5px); }
}

.error-icon {
    animation: shake 2s ease-in-out infinite;
}

.error-btn-primary, .error-btn-secondary {
    transition: all 0.3s ease;
    border: 2px solid white;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    cursor: pointer;
}

.error-btn-primary {
    background: white;
    color: #222222;
}

.error-btn-primary:hover {
    background: transparent;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(255, 255, 255, 0.3);
}

.error-btn-secondary {
    background: transparent;
    color: white;
}

.error-btn-secondary:hover {
    background: white;
    color: #222222;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(255, 255, 255, 0.2);
}
</style>
@endsection
