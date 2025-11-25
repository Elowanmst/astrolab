@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-[#222222] flex items-center justify-center px-4">
    <div class="text-center max-w-2xl">
        <!-- Icône email -->
        <div class="mb-8">
            <div class="w-24 h-24 bg-blue-500 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
            </div>
        </div>
        
        <!-- Titre principal -->
        <h1 class="text-5xl font-bold text-white mb-6">VÉRIFIEZ VOTRE EMAIL</h1>
        
        <!-- Message d'information -->
        <div class="space-y-4 mb-8">
            <p class="text-xl text-gray-300">
                Un lien de vérification a été envoyé à votre adresse email.
            </p>
            <p class="text-lg text-gray-400">
                Cliquez sur le lien dans l'email pour activer votre compte.
            </p>
            <p class="text-gray-500">
                Vérifiez également votre dossier de spam si vous ne trouvez pas l'email.
            </p>
        </div>

        @if (session('message'))
            <div class="bg-green-500 bg-opacity-10 border border-green-500 rounded-lg p-4 mb-8">
                <p class="text-green-400">
                    {{ session('message') }}
                </p>
            </div>
        @endif
        
        @if (session('error'))
            <div class="bg-red-500 bg-opacity-10 border border-red-500 rounded-lg p-4 mb-8">
                <p class="text-red-400">
                    {{ session('error') }}
                </p>
            </div>
        @endif
        
        <!-- Informations utiles -->
        <div class="bg-white bg-opacity-5 rounded-lg p-6 mb-8">
            <h3 class="text-xl font-bold text-white mb-4">QUE FAIRE ?</h3>
            <div class="space-y-3 text-gray-400 text-left">
                <p>📧 Vérifiez votre boîte de réception</p>
                <p>📁 Regardez dans les spams/courriers indésirables</p>
                <p>⏱️ L'email peut prendre quelques minutes à arriver</p>
                <p>🔄 Vous pouvez renvoyer l'email si nécessaire</p>
            </div>
        </div>
        
        <!-- Boutons d'action -->
        <div class="space-y-4">
            @if(auth()->user()->canSendVerificationEmail())
                <form method="POST" action="{{ route('verification.send') }}" class="mb-4">
                    @csrf
                    <button type="submit" class="email-verify-btn px-8 py-4 text-lg">
                        RENVOYER L'EMAIL DE VÉRIFICATION
                    </button>
                </form>
            @else
                <div class="mb-4">
                    <button disabled class="email-verify-btn-disabled px-8 py-4 text-lg cursor-not-allowed">
                        RENVOYER L'EMAIL DE VÉRIFICATION
                    </button>
                    <div class="mt-3 p-4 bg-orange-500 bg-opacity-10 border border-orange-500 rounded-lg">
                        <p class="text-orange-400 text-sm">
                            ⏱️ {{ auth()->user()->getVerificationEmailCooldownMessage() }}
                        </p>
                        <div class="mt-2">
                            <div class="countdown-timer text-orange-300 font-mono text-lg" 
                                 data-countdown="{{ auth()->user()->getVerificationEmailCooldownSeconds() }}">
                                <span class="minutes">{{ floor(auth()->user()->getVerificationEmailCooldownSeconds() / 60) }}</span>:
                                <span class="seconds">{{ sprintf('%02d', auth()->user()->getVerificationEmailCooldownSeconds() % 60) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            
            <div class="flex justify-center space-x-4">
                <a href="{{ route('home') }}" class="btn-secondary px-6 py-3">
                    RETOUR À L'ACCUEIL
                </a>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="btn-secondary px-6 py-3">
                        SE DÉCONNECTER
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Support -->
        <div class="mt-12 text-sm text-gray-500">
            <p>Problème avec la vérification ? Contactez-nous :</p>
            <p><a href="mailto:support@astrolab-boutique.com" class="text-gray-400 hover:text-white">support@astrolab-boutique.com</a></p>
        </div>
    </div>
</div>

<style>
.email-verify-btn {
    background: transparent;
    border: 2px solid #6b7280;
    color: #6b7280;
    border-radius: 8px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    transition: all 0.3s ease;
    cursor: pointer;
    display: inline-block;
    text-decoration: none;
}

.email-verify-btn:hover {
    background: #6b7280;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(107, 114, 128, 0.3);
}

.email-verify-btn:active {
    transform: translateY(0);
    box-shadow: 0 4px 15px rgba(107, 114, 128, 0.2);
}

.email-verify-btn-disabled {
    background: transparent;
    border: 2px solid #4b5563;
    color: #4b5563;
    border-radius: 8px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    display: inline-block;
    text-decoration: none;
    opacity: 0.5;
}

.countdown-timer {
    text-align: center;
    font-size: 1.2em;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const timer = document.querySelector('.countdown-timer');
    if (!timer) return;
    
    let remainingSeconds = parseInt(timer.dataset.countdown);
    
    const updateTimer = () => {
        const minutes = Math.floor(remainingSeconds / 60);
        const seconds = remainingSeconds % 60;
        
        timer.querySelector('.minutes').textContent = minutes;
        timer.querySelector('.seconds').textContent = seconds.toString().padStart(2, '0');
        
        if (remainingSeconds <= 0) {
            location.reload(); // Recharger la page pour réactiver le bouton
            return;
        }
        
        remainingSeconds--;
    };
    
    // Mettre à jour immédiatement
    updateTimer();
    
    // Mettre à jour chaque seconde
    setInterval(updateTimer, 1000);
});
</script>
@endsection
