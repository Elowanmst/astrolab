@extends('layouts.app')

@section('content')
<div class="min-h-screen text-white" style="background-color: rgb(34,34,34);">
    
    @auth
        <div class="text-white p-4 text-center border-b border-gray-700" style="background-color: rgb(34,34,34);">
            <p class="text-gray-300">
                Bienvenue, <span class="text-white font-semibold uppercase">{{ Auth::user()->name }}</span> !
                <a href="{{ route('profile') }}" class="text-blue-400 hover:text-blue-300 ml-2 underline transition-all duration-300">Voir mon profil</a>
            </p>
        </div>
    @endauth
    
    <!-- Bannière en haut de page (gardée sans modification) -->
    <section class="bg-home">
        <div class=""></div>
    </section>
    
    <!-- Section principale avec titre et description -->
    <div class="w-full text-center py-20">
        <div class="home-title">
            <h1 class="text-6xl font-bold mb-6">ASTROLAB</h1>
            <div class="flex justify-center items-center mb-8">
                <div class="w-1 h-16 bg-white mr-4"></div>
                <p class="text-gray-300 text-lg tracking-wider">
                    DES ÉDITIONS ÉPHÉMÈRES EXCLUSIVES IMAGINÉES PAR DES ILLUSTRATEURS INDÉPENDANTS
                </p>
                <div class="w-1 h-16 bg-white ml-4"></div>
            </div>
        </div>
        
        <div class="w-full max-w-6xl h-px bg-white shadow-[0_0_10px_2px_rgba(255,255,255,0.7)] mx-auto mb-20"></div>
        
        <!-- Section Boutique -->
        <section class="max-w-7xl mx-auto px-4 mb-32 scroll-animate">
            <div class="text-center mb-16">
                <h2 class="text-5xl font-bold mb-8" style="font-family: var(--astro-font-family); font-weight: var(--astro-font-weight); letter-spacing: var(--astro-letter-spacing);">COLLECTION STELLAE 01</h2>
                <p class="text-gray-300 text-xl mb-8" style="font-family: var(--astro-font-family); letter-spacing: 1px;">DÉCOUVREZ NOTRE PREMIÈRE COLLECTION EXCLUSIVE</p>
            </div>
            
            <!-- Grille de produits -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-16">
                @php $visibleProducts = $products->take(6); @endphp
                @foreach ($visibleProducts as $product)
                    <div class="glass-card rounded-lg p-6 card-hover-effect cursor-pointer scroll-animate" 
                         onclick="window.location='{{ route('products.show', $product) }}'">
                        <div class="product-image-container bg-gray-900 rounded-lg p-4 mb-4">
                            <img class="w-full h-64 object-cover rounded-lg" 
                                src="{{ $product->getFirstMediaUrl('products', 'thumb') ?: asset('default-image.jpg') }}" 
                                alt="Image de {{ $product->name }}">
                        </div>
                        <h3 class="text-xl font-bold mb-2 uppercase" style="font-family: var(--astro-font-family); font-weight: var(--astro-font-weight); letter-spacing: var(--astro-letter-spacing);">{{ $product->name }}</h3>
                        <p class="text-gray-300 mb-4" style="font-family: var(--astro-font-family);">Coupe classique • Noir ou Blanc</p>
                        <p class="text-lg font-bold text-white" style="font-family: var(--astro-font-family); font-weight: var(--astro-font-weight);">{{ $product->price }} €</p>
                    </div>
                @endforeach
            </div>
            
            @if($products->count() > 6)
                <div class="text-center mb-16 scroll-animate">
                    <button class="btn-modern text-white px-8 py-3 rounded-lg font-bold uppercase">
                        VOIR PLUS
                    </button>
                </div>
            @endif
            
            <!-- Section Précommande -->
            <div class="glass-card rounded-lg p-8 card-hover-effect scroll-animate">
                <h3 class="text-3xl font-bold text-center mb-4" style="font-family: var(--astro-font-family); font-weight: var(--astro-font-weight); letter-spacing: var(--astro-letter-spacing);">PRÉCOMMANDE BIENTÔT DISPONIBLE</h3>
                <p class="text-gray-300 text-center mb-8" style="font-family: var(--astro-font-family);">Soyez les premiers informés du lancement</p>
                <div class="text-center">
                    <button class="bg-white text-gray-900 px-8 py-3 rounded-lg font-bold hover:bg-gray-200 transition-all duration-200 uppercase" style="font-family: var(--astro-font-family); font-weight: var(--astro-font-weight); letter-spacing: 1px;">
                        ÊTRE NOTIFIÉ
                    </button>
                </div>
            </div>
        </section>
        
        <!-- Section Illustrateurs -->
        <section class="max-w-7xl mx-auto px-4 mb-32 scroll-animate">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold mb-8" style="font-family: var(--astro-font-family); font-weight: var(--astro-font-weight); letter-spacing: var(--astro-letter-spacing);">ILLUSTRATEURS</h2>
                <p class="text-gray-300 text-xl" style="font-family: var(--astro-font-family);">
                    Découvrez les artistes derrière nos créations exclusives
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="glass-card rounded-lg p-8 text-center card-hover-effect scroll-animate">
                    <div class="w-24 h-24 bg-gray-600 rounded-full mx-auto mb-6"></div>
                    <h3 class="text-2xl font-bold mb-4" style="font-family: var(--astro-font-family); font-weight: var(--astro-font-weight); letter-spacing: var(--astro-letter-spacing);">MATHIS DOUILLARD</h3>
                    <p class="text-gray-300 mb-6" style="font-family: var(--astro-font-family);">Illustrateur & Designer graphique</p>
                    <button class="bg-white text-gray-900 px-6 py-2 rounded-lg font-bold hover:bg-gray-200 transition-all duration-200 uppercase" style="font-family: var(--astro-font-family); font-weight: var(--astro-font-weight); letter-spacing: 1px;">
                        DÉCOUVRIR
                    </button>
                </div>
                
                <div class="glass-card rounded-lg p-8 text-center card-hover-effect scroll-animate">
                    <div class="w-24 h-24 bg-gray-600 rounded-full mx-auto mb-6"></div>
                    <h3 class="text-2xl font-bold mb-4" style="font-family: var(--astro-font-family); font-weight: var(--astro-font-weight); letter-spacing: var(--astro-letter-spacing);">AXEL CHAPET</h3>
                    <p class="text-gray-300 mb-6" style="font-family: var(--astro-font-family);">Illustrateur & Designer graphique</p>
                    <button class="bg-white text-gray-900 px-6 py-2 rounded-lg font-bold hover:bg-gray-200 transition-all duration-200 uppercase" style="font-family: var(--astro-font-family); font-weight: var(--astro-font-weight); letter-spacing: 1px;">
                        DÉCOUVRIR
                    </button>
                </div>
            </div>
        </section>
        
        <!-- Section Contact -->
        <section class="max-w-7xl mx-auto px-4 mb-32 scroll-animate">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold mb-8" style="font-family: var(--astro-font-family); font-weight: var(--astro-font-weight); letter-spacing: var(--astro-letter-spacing);">CONTACTEZ-NOUS</h2>
                <p class="text-gray-300 text-xl" style="font-family: var(--astro-font-family);">
                    Une question, un projet personnalisé ou besoin d'informations ?
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
                <!-- Téléphone -->
                <div class="contact-card rounded-lg p-6 text-center card-hover-effect scroll-animate">
                    <div class="contact-icon mx-auto">
                        <i class="fas fa-phone text-white text-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3" style="font-family: var(--astro-font-family); font-weight: var(--astro-font-weight); letter-spacing: var(--astro-letter-spacing);">TÉLÉPHONE</h3>
                    <p class="text-gray-300 mb-4" style="font-family: var(--astro-font-family);">Lundi au Vendredi<br>9h - 18h</p>
                    <a href="tel:0600000000" class="text-white font-bold hover:text-gray-300 transition-colors duration-200" style="font-family: var(--astro-font-family); font-weight: var(--astro-font-weight);">06 00 00 00 00</a>
                </div>
                
                <!-- Email -->
                <div class="contact-card rounded-lg p-6 text-center card-hover-effect scroll-animate">
                    <div class="contact-icon mx-auto">
                        <i class="fas fa-envelope text-white text-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3" style="font-family: var(--astro-font-family); font-weight: var(--astro-font-weight); letter-spacing: var(--astro-letter-spacing);">EMAIL</h3>
                    <p class="text-gray-300 mb-4" style="font-family: var(--astro-font-family);">Réponse sous 24h<br>7j/7</p>
                    <a href="mailto:contact@astrolab.fr" class="text-white font-bold hover:text-gray-300 transition-colors duration-200" style="font-family: var(--astro-font-family); font-weight: var(--astro-font-weight);">contact@astrolab.fr</a>
                </div>
                
                <!-- Adresse -->
                <div class="contact-card rounded-lg p-6 text-center card-hover-effect scroll-animate">
                    <div class="contact-icon mx-auto">
                        <i class="fas fa-map-marker-alt text-white text-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3" style="font-family: var(--astro-font-family); font-weight: var(--astro-font-weight); letter-spacing: var(--astro-letter-spacing);">ATELIER</h3>
                    <p class="text-gray-300 mb-4" style="font-family: var(--astro-font-family);">Visite sur RDV<br>Paris, France</p>
                    <span class="text-white font-bold" style="font-family: var(--astro-font-family); font-weight: var(--astro-font-weight);">Paris 11ème</span>
                </div>
            </div>
            
            <!-- Newsletter améliorée -->
            <div class="contact-card rounded-lg p-8 text-center scroll-animate">
                <div class="max-w-2xl mx-auto">
                    <div class="contact-icon mx-auto mb-6">
                        <i class="fas fa-bell text-white text-xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-4" style="font-family: var(--astro-font-family); font-weight: var(--astro-font-weight); letter-spacing: var(--astro-letter-spacing);">NEWSLETTER EXCLUSIVE</h3>
                    <p class="text-gray-300 mb-8" style="font-family: var(--astro-font-family);">
                        Soyez les premiers informés de nos nouvelles collections, éditions limitées et événements privés. 
                        <br><strong>Accès privilégié aux préventes</strong>
                    </p>
                    <div class="flex flex-col md:flex-row gap-4 max-w-md mx-auto">
                        <input type="email" placeholder="Votre email" 
                               class="flex-1 contact-card border border-gray-600 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:border-white focus:ring-1 focus:ring-white transition-all duration-200" 
                               style="font-family: var(--astro-font-family);">
                        <button class="bg-white text-gray-900 px-6 py-3 rounded-lg font-bold hover:bg-gray-200 transition-all duration-200 uppercase" 
                                style="font-family: var(--astro-font-family); font-weight: var(--astro-font-weight); letter-spacing: 1px;">
                            S'ABONNER
                        </button>
                    </div>
                    <p class="text-gray-400 text-sm mt-4" style="font-family: var(--astro-font-family);">
                        Pas de spam, désinscription possible à tout moment
                    </p>
                </div>
            </div>
        </section>
    </div>

    <!-- Footer -->
    <footer class="border-t border-gray-800" style="background-color: rgb(24,24,24);">
        <div class="max-w-7xl mx-auto px-4 py-16">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold mb-4">ASTROLAB</h2>
                <p class="text-gray-300">Créations exclusives • Éditions limitées • Qualité premium</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
                <div class="text-center">
                    <h3 class="text-xl font-bold mb-4">LIENS RAPIDES</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-300 hover:text-white transition-colors duration-200">Boutique</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-white transition-colors duration-200">Illustrateurs</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-white transition-colors duration-200">Contact</a></li>
                    </ul>
                </div>
                
                <div class="text-center">
                    <h3 class="text-xl font-bold mb-4">INFORMATIONS</h3>
                    <ul class="space-y-2">
                        <li><span class="text-gray-300">Éditions limitées</span></li>
                        <li><span class="text-gray-300">Livraison France</span></li>
                        <li><span class="text-gray-300">Qualité premium</span></li>
                    </ul>
                </div>
                
                <div class="text-center">
                    <h3 class="text-xl font-bold mb-4">SUIVEZ-NOUS</h3>
                    <a href="https://instagram.com/" target="_blank" 
                       class="inline-block text-gray-300 hover:text-white transition-colors duration-200">
                        <i class="fab fa-instagram text-2xl"></i>
                    </a>
                </div>
            </div>
            
            <div class="border-t border-gray-700 pt-8 text-center">
                <p class="text-gray-300">
                    © 2025 ASTROLAB - Tous droits réservés | 
                    Créé par <a href="https://ec-craft.fr" target="_blank" class="text-blue-400 hover:text-blue-300 transition-colors duration-200">ec-craft.fr</a>
                </p>
            </div>
        </div>
    </footer>
</div>

<script>
// Animation au scroll - Version optimisée
document.addEventListener('DOMContentLoaded', function() {
    // Configuration de l'observer
    const observerOptions = {
        threshold: 0.15,
        rootMargin: '0px 0px -100px 0px'
    };

    let animationQueue = [];

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                // Ajouter à la queue d'animation
                const delay = entry.target.style.animationDelay || '0s';
                const delayMs = parseFloat(delay) * 1000;
                
                animationQueue.push({
                    element: entry.target,
                    delay: delayMs
                });
                
                observer.unobserve(entry.target);
            }
        });
        
        // Traiter la queue d'animations
        processAnimationQueue();
    }, observerOptions);

    function processAnimationQueue() {
        // Trier par délai
        animationQueue.sort((a, b) => a.delay - b.delay);
        
        animationQueue.forEach((item, index) => {
            setTimeout(() => {
                item.element.classList.add('visible');
            }, item.delay);
        });
        
        // Vider la queue
        animationQueue = [];
    }

    // Observer tous les éléments avec la classe scroll-animate
    const elementsToAnimate = document.querySelectorAll('.scroll-animate');
    elementsToAnimate.forEach((el, index) => {
        // Ajouter un délai progressif si pas déjà défini
        if (!el.style.animationDelay) {
            el.style.transitionDelay = (index * 0.1) + 's';
        }
        observer.observe(el);
    });

    // Performance: Réduire les animations sur mobile si nécessaire
    if (window.innerWidth < 768) {
        const style = document.createElement('style');
        style.textContent = `
            .scroll-animate {
                transition-duration: 0.4s !important;
            }
        `;
        document.head.appendChild(style);
    }
});
</script>

@endsection