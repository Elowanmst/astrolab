@extends('layouts.app')

@section('title', 'Erreur - Astrolab')

@section('content')
<div class="min-h-screen bg-[#222222] flex items-center justify-center px-4">
    <div class="text-center max-w-2xl">
        <!-- Icône d'erreur -->
        <div class="mb-8">
            <div class="w-32 h-32 bg-grey-700 rounded-full flex items-center justify-center mx-auto mb-6 border-4 border-white error-icon">
                @if(isset($exception) && method_exists($exception, 'getStatusCode'))
                    <span class="text-4xl font-bold text-white">{{ $exception->getStatusCode() }}</span>
                @else
                    <i class="fas fa-exclamation text-4xl text-white"></i>
                @endif
            </div>
        </div>
        
        <!-- Titre principal -->
        <h1 class="text-5xl font-bold text-white mb-6">ASTROLAB</h1>
        <h2 class="text-3xl font-bold text-white mb-6">
            @if(isset($exception) && method_exists($exception, 'getStatusCode'))
                ERREUR {{ $exception->getStatusCode() }}
            @else
                ERREUR
            @endif
        </h2>
        
        <!-- Message d'erreur -->
        <div class="space-y-4 mb-8">
            <p class="text-xl text-gray-300">
                @if(isset($exception) && $exception->getMessage())
                    {{ $exception->getMessage() }}
                @else
                    Une erreur inattendue s'est produite.
                @endif
            </p>
            <p class="text-lg text-gray-400">
                Notre équipe a été notifiée et travaille sur le problème.
            </p>
        </div>
        
        <!-- Boutons d'action -->
        <div class="space-y-4">
            <button onclick="history.back()" class="error-btn-primary inline-block px-8 py-4 text-lg">
                <i class="fas fa-arrow-left mr-2"></i>
                RETOUR
            </button>
            <div class="flex justify-center space-x-4">
                <a href="{{ route('home') }}" class="error-btn-secondary px-6 py-3">
                    <i class="fas fa-home mr-2"></i>
                    RETOUR À L'ACCUEIL
                </a>
                <button onclick="window.location.reload()" class="error-btn-secondary px-6 py-3">
                    <i class="fas fa-redo mr-2"></i>
                    ACTUALISER
                </button>
            </div>
        </div>
        
        <!-- Détails techniques (mode debug uniquement) -->
        @if(config('app.debug') && isset($exception))
            <div class="mt-12 p-4 bg-gray-900 bg-opacity-50 rounded-lg text-left">
                <h3 class="text-white font-bold mb-2">Détails techniques :</h3>
                <div class="text-sm text-gray-300 space-y-1">
                    @if(method_exists($exception, 'getFile'))
                        <p><strong>Fichier :</strong> {{ $exception->getFile() }}</p>
                    @endif
                    @if(method_exists($exception, 'getLine'))
                        <p><strong>Ligne :</strong> {{ $exception->getLine() }}</p>
                    @endif
                    @if($exception->getMessage())
                        <p><strong>Message :</strong> {{ $exception->getMessage() }}</p>
                    @endif
                </div>
            </div>
        @endif
        
        <!-- Support -->
        <div class="mt-8 p-4 bg-white bg-opacity-5 rounded-lg">
            <p class="text-sm text-gray-400">
                <i class="fas fa-life-ring mr-2"></i>
                Besoin d'aide ? Contactez-nous : 
                <a href="mailto:support@astrolab-boutique.com" class="text-white hover:underline">
                    support@astrolab-boutique.com
                </a>
            </p>
        </div>
    </div>
</div>

<!-- Animation CSS -->
<style>
@keyframes bounce {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-5px); }
}

.error-icon {
    animation: bounce 2s ease-in-out infinite;
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
