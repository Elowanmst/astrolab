@extends('layouts.app')

@section('title', 'Accès refusé - Astrolab')

@section('content')
<div class="min-h-screen bg-[#222222] flex items-center justify-center px-4">
    <div class="text-center max-w-2xl">
        <!-- Icône d'erreur -->
        <div class="mb-8">
            <div class="w-32 h-32 bg-yellow-600 rounded-full flex items-center justify-center mx-auto mb-6 border-4 border-white error-icon">
                <i class="fas fa-lock text-4xl text-white"></i>
            </div>
        </div>
        
        <!-- Titre principal -->
        <h1 class="text-5xl font-bold text-white mb-6">ASTROLAB</h1>
        <h2 class="text-3xl font-bold text-white mb-6">ACCÈS REFUSÉ</h2>
        
        <!-- Message d'erreur -->
        <div class="space-y-4 mb-8">
            <p class="text-xl text-gray-300">
                Cette zone est classifiée top secret...
            </p>
            <p class="text-lg text-gray-400">
                Vous n'avez pas l'autorisation d'accéder à cette page.
            </p>
        </div>
        
        <!-- Boutons d'action -->
        <div class="space-y-4">
            @auth
                <a href="{{ url('/profile') }}" class="error-btn-primary inline-block px-8 py-4 text-lg">
                    <i class="fas fa-user mr-2"></i>
                    MON PROFIL
                </a>
            @else
                <a href="{{ url('/checkout/auth') }}" class="error-btn-primary inline-block px-8 py-4 text-lg">
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    SE CONNECTER
                </a>
            @endauth
            
            <div class="flex justify-center space-x-4">
                <a href="{{ route('home') }}" class="error-btn-secondary px-6 py-3">
                    <i class="fas fa-home mr-2"></i>
                    RETOUR À L'ACCUEIL
                </a>
                <a href="{{ url('/#boutique') }}" class="error-btn-secondary px-6 py-3">
                    <i class="fas fa-shopping-bag mr-2"></i>
                    VOIR LA BOUTIQUE
                </a>
            </div>
        </div>
        
        <!-- Note personnelle -->
        <div class="mt-12 p-4 bg-white bg-opacity-5 rounded-lg">
            <p class="text-sm text-gray-400">
                <i class="fas fa-shield-alt mr-2"></i>
                Connectez-vous pour accéder à votre espace personnel
            </p>
        </div>
    </div>
</div>

<!-- Animation CSS -->
<style>
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.7; }
}

.error-icon {
    animation: pulse 2s ease-in-out infinite;
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
