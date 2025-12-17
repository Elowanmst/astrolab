@extends('layouts.app')

@section('title', 'Page non trouvée - Astrolab')

@section('content')
<div class="min-h-screen bg-[#222222] flex items-center justify-center px-4">
    <div class="text-center max-w-2xl">
        <!-- Icône d'erreur -->
        <div class="mb-8">
            <div class="w-32 h-32 bg-grey-600 rounded-full flex items-center justify-center mx-auto mb-6 border-4 border-white error-icon">
                <span class="text-6xl font-bold text-white">404</span>
            </div>
        </div>
        
        <!-- Titre principal -->
        <h1 class="text-5xl font-bold text-white mb-6">ASTROLAB</h1>
        <h2 class="text-3xl font-bold text-white mb-6">PAGE NON TROUVÉE</h2>
        
        <!-- Message d'erreur -->
        <div class="space-y-4 mb-8">
            <p class="text-xl text-gray-300">
                La page que vous recherchez semble avoir disparu dans l'espace...
            </p>
            <p class="text-lg text-gray-400">
                Mais ne vous inquiétez pas, nos éditions exclusives vous attendent !
            </p>
        </div>
        
        <!-- Boutons d'action -->
        <div class="space-y-4">
            <a href="{{ route('home') }}" class="error-btn-primary inline-block px-8 py-4 text-lg">
                <i class="fas fa-home mr-2"></i>
                RETOUR À L'ACCUEIL
            </a>
            <div class="flex justify-center space-x-4">
                <a href="{{ url('/#boutique') }}" class="error-btn-secondary px-6 py-3">
                    <i class="fas fa-shopping-bag mr-2"></i>
                    DÉCOUVRIR LA BOUTIQUE
                </a>
                <a href="{{ url('/#contact') }}" class="error-btn-secondary px-6 py-3">
                    <i class="fas fa-envelope mr-2"></i>
                    NOUS CONTACTER
                </a>
            </div>
        </div>
        
        <!-- Note personnelle -->
        <div class="mt-12 p-4 bg-white bg-opacity-5 rounded-lg">
            <p class="text-sm text-gray-400">
                <i class="fas fa-rocket mr-2"></i>
                Équipe ASTROLAB - Créations exclusives depuis 2025
            </p>
        </div>
    </div>
</div>

<!-- Animation CSS -->
<style>
@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
}

.error-icon {
    animation: float 3s ease-in-out infinite;
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
