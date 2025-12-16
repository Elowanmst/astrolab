@extends('layouts.app')

@section('title', 'Session expirée - Astrolab')

@section('content')
<div class="min-h-screen bg-[#222222] flex items-center justify-center px-4">
    <div class="text-center max-w-2xl">
        <!-- Icône d'erreur -->
        <div class="mb-8">
            <div class="w-32 h-32 bg-grey-600 rounded-full flex items-center justify-center mx-auto mb-6 border-4 border-white error-icon">
                <i class="fas fa-clock text-4xl text-white"></i>
            </div>
        </div>
        
        <!-- Titre principal -->
        <h1 class="text-5xl font-bold text-white mb-6">ASTROLAB</h1>
        <h2 class="text-3xl font-bold text-white mb-6">SESSION EXPIRÉE</h2>
        
        <!-- Message d'erreur -->
        <div class="space-y-4 mb-8">
            <p class="text-xl text-gray-300">
                Votre session a expiré pour des raisons de sécurité.
            </p>
            <p class="text-lg text-gray-400">
                Veuillez rafraîchir la page et réessayer votre action.
            </p>
        </div>
        
        <!-- Boutons d'action -->
        <div class="space-y-4">
            <button onclick="window.location.reload()" class="error-btn-primary inline-block px-8 py-4 text-lg">
                <i class="fas fa-sync-alt mr-2"></i>
                ACTUALISER LA PAGE
            </button>
            <div class="flex justify-center space-x-4">
                <a href="{{ route('home') }}" class="error-btn-secondary px-6 py-3">
                    <i class="fas fa-home mr-2"></i>
                    RETOUR À L'ACCUEIL
                </a>
                @auth
                    <a href="{{ url('/profile') }}" class="error-btn-secondary px-6 py-3">
                        <i class="fas fa-user mr-2"></i>
                        MON PROFIL
                    </a>
                @endauth
            </div>
        </div>
        
        <!-- Informations utiles -->
        <div class="mt-12 p-4 bg-white bg-opacity-5 rounded-lg">
            <h3 class="text-lg font-bold text-white mb-2">Pourquoi cette erreur ?</h3>
            <div class="text-sm text-gray-400 space-y-1">
                <p>🔒 Protection contre les attaques CSRF</p>
                <p>⏱️ Session inactive trop longtemps</p>
                <p>🔄 Rafraîchir la page résout généralement le problème</p>
            </div>
        </div>
    </div>
</div>

<!-- Animation CSS -->
<style>
@keyframes rotate {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.error-icon {
    animation: rotate 4s linear infinite;
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
