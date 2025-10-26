@extends('layouts.app')

@section('content')
<div class="landing-container" style="padding-top: 0;">
    
    <!-- Hero Section avec bannière dynamique -->
    <section class="bg-home hero-section" style="height: 70vh;">
        <div class="hero-banner" style="flex: 1; background-image: url('{{ $homeSettings['hero_image'] ? asset('storage/' . $homeSettings['hero_image']) : '/assets/img/BANIERE-COLLECTION-SITE-ASTROLAB.webp' }}'); background-size: cover; background-position: center; background-repeat: no-repeat; position: relative; display: flex; align-items: center; justify-content: center;">
            <div class="hero-overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0, 0, 0, 0.1);"></div>
        </div>
        <div class="hero-content" style="background: rgba(34, 34, 34, 0.95); padding: 40px 20px; text-align: center; position: relative; z-index: 2;">
            <div class="hero-text">
                <p class="hero-subtitle" style="font-size: clamp(0.8rem, 2.5vw, 1.2rem); line-height: 1.2; word-spacing: -0.1em;">| DES ÉDITIONS ÉPHÉMÈRES EXCLUSIVES IMAGINÉES PAR DES ILLUSTRATEURS INDÉPENDANTS |</p>
            </div>
        </div>
    </section>
    
    <!-- Séparateur lumineux -->
    <div class="divider"></div>
    
    <!-- Section Boutique -->
    <section id="boutique" class="section-container text-center">
        <div class="max-width">
            @if($collection)
                <h2 class="section-title">{{ strtoupper($collection->name) }}</h2>
                @if($collection->description)
                    <p class="section-subtitle">{{ strtoupper($collection->description) }}</p>
                @endif
            @else
                <h2 class="section-title">COLLECTION STELLAE 01</h2>
                <p class="section-subtitle">DÉCOUVREZ NOTRE PREMIÈRE COLLECTION EXCLUSIVE</p>
            @endif
            
            <!-- Grille de produits en utilisant les données dynamiques -->
            <div class="products-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; max-width: 1200px; margin: 0 auto;">
                @php $visibleProducts = $products->take(6); @endphp
                @foreach ($visibleProducts as $product)
                    <div class="card product-card" onclick="navigateToProduct('{{ route('products.show', $product) }}', event)">
                        <div class="product-image-container">
                            @if($product->getFirstMediaUrl('products', 'thumb'))
                                <img src="{{ $product->getFirstMediaUrl('products', 'thumb') }}" 
                                     alt="Image de {{ $product->name }}" 
                                     class="product-image">
                            @else
                                <div class="product-placeholder">
                                    <span class="placeholder-text">Image bientôt disponible</span>
                                </div>
                            @endif
                        </div>
                        <h3 class="card-title">{{ strtoupper($product->name) }}</h3>
                        <p class="card-text">Coupe classique • Noir ou Blanc</p>
                        <p class="card-text" style="color: var(--astro-text-primary); font-weight: bold;">{{ $product->price }} €</p>
                    </div>
                @endforeach
            </div>
            
            <!-- Bouton vers la boutique -->
            {{-- <div class="text-center space-y">
                <a href="{{ route('products.index') }}" class="btn-home">
                    VOIR TOUTE LA COLLECTION
                </a>
            </div> --}}
            
            <!-- Call to Action -->
            {{-- <div class="text-center space-y">
                <div class="preorder-notice">
                    <h3 class="preorder-title">PRÉCOMMANDE BIENTÔT DISPONIBLE</h3>
                    <p class="preorder-subtitle">Soyez les premiers informés du lancement de notre première collection exclusive</p>
                </div>
                <div class="button-container" style="gap: 20px; margin-top: 32px;">
                    <a href="#contact" class="btn-home">
                        NOUS CONTACTER
                    </a>
                    <a href="#illustrateurs" class="btn-home">
                        DÉCOUVRIR LES ILLUSTRATEURS
                    </a>
                </div>
            </div> --}}
        </div>
    </section>
    
    <!-- Section Illustrateurs -->
    <section id="illustrateurs" class="section-container">
        <div class="max-width text-center">
            <h2 class="section-title">ILLUSTRATEURS - GRAPHISTES</h2>
            <h3 class="section-subtitle">| SUIVEZ MATHIS DOUILLARD & AXEL CHAPET SUR LEURS RÉSEAUX POUR DÉCOUVRIR LEUR UNIVERS |</h3>
            
            <div class="grid-3">
                <div class="card">
                    <div class="card-icon" style="width: 120px; height: 120px; background-color: #666; border-radius: 50%; margin: 0 auto 24px;"></div>
                    <h4 class="card-title">MATHIS DOUILLARD</h4>
                    <p class="card-text">Illustrateur & Designer graphique</p>
                    <a href="#" class="btn-home">SUIVRE MATHIS</a>
                </div>
                <div class="card">
                    <div class="card-icon" style="width: 120px; height: 120px; background-color: #666; border-radius: 50%; margin: 0 auto 24px;"></div>
                    <h4 class="card-title">AXEL CHAPET</h4>
                    <p class="card-text">Illustrateur & Designer graphique</p>
                    <a href="#" class="btn-home">SUIVRE AXEL</a>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Section Newsletter -->
    {{-- <section id="newsletter" class="section-container footer-section">
        <div class="max-width text-center">
            <h2 class="section-title">RESTEZ INFORMÉ</h2>
            <p class="section-subtitle">Soyez le premier à découvrir nos nouvelles collections et bénéficiez d'offres exclusives</p>
            <div class="newsletter-container">
                <input type="email" placeholder="Votre adresse email" class="form-input newsletter-form" style="width: 400px;">
                <button class="btn-home" style="margin-top: 0;">
                    S'ABONNER
                </button>
            </div>
        </div>
    </section> --}}
    
    <!-- Section Contact -->
    <section id="contact" class="section-container">
        <div class="max-width">
            <div class="text-center space-y">
                <h2 class="section-title">CONTACT</h2>
                <h3 class="section-subtitle">| CONTACTEZ-NOUS POUR TOUTE DEMANDE D'INFORMATION |</h3>
            </div>
            
            {{-- <div class="grid-3" style="grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));">
                <!-- Informations de contact -->
                <div class="card">
                    <h4 class="card-title">CONTACTEZ-NOUS</h4>
                    <div class="card-text">
                        <div class="contact-info-item">
                            <div class="contact-icon">
                                <i class="fas fa-phone"></i>
                            </div>
                            <div class="contact-details">
                                <h4>Téléphone</h4>
                                <p>06 00 00 00 00</p>
                            </div>
                        </div>
                        <div class="contact-info-item">
                            <div class="contact-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="contact-details">
                                <h4>Email</h4>
                                <p>contact@astrolab.fr</p>
                            </div>
                        </div>
                        <div class="contact-info-item">
                            <div class="contact-icon">
                                <i class="fab fa-instagram"></i>
                            </div>
                            <div class="contact-details">
                                <h4>Instagram</h4>
                                <a href="https://instagram.com/" target="_blank" style="color: white; text-decoration: none;">
                                    @astrolab_official
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- CTA urgence -->
                    <div class="card preorder-cta-card" style="margin-top: 24px;">
                        <h4 class="card-title" style="color: white;">ENVIE DE PRÉCOMMANDER ?</h4>
                        <p class="card-text" style="color: rgba(255,255,255,0.9);">Contactez-nous dès maintenant pour réserver votre pièce avant la fin de la collection</p>
                        <a href="mailto:contact@astrolab.fr" class="btn-home">
                            ENVOYER UN EMAIL
                        </a>
                    </div>
                </div> --}}
                
                <!-- Formulaire de contact -->
                <div class="form-container">
                    <h4 class="card-title">ENVOYEZ-NOUS UN MESSAGE</h4>
                    <form>
                        <div class="form-group">
                            <label class="form-label">Nom complet</label>
                            <input type="text" name="name" required class="form-input" placeholder="Votre nom complet">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" required class="form-input" placeholder="votre@email.com">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Sujet</label>
                            <select name="subject_type" class="form-input">
                                <option value="">Choisissez un sujet</option>
                                <option value="precommande">Précommande</option>
                                <option value="info">Information produit</option>
                                <option value="partenariat">Partenariat</option>
                                <option value="autre">Autre</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Message</label>
                            <textarea name="message" rows="4" required class="form-input" placeholder="Décrivez votre demande..."></textarea>
                        </div>
                        <button type="submit" class="btn-home" style="width: 100%;">
                            ENVOYER LE MESSAGE
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- JavaScript pour les animations -->
<script>
// === INITIALISATION AU CHARGEMENT DE LA PAGE ===
document.addEventListener('DOMContentLoaded', function() {
    // === ANIMATIONS AU SCROLL ===
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    }, observerOptions);

    // Observer les éléments à animer
    const elementsToAnimate = document.querySelectorAll('.section-container, .card, .product-card');
    elementsToAnimate.forEach(el => observer.observe(el));
});

// === SMOOTH SCROLL ===
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// === NAVIGATION VERS PRODUIT ===
function navigateToProduct(url, event) {
    if (event) {
        event.preventDefault();
    }
    window.location.href = url;
}
</script>

@endsection