@extends('layouts.landing')

@section('content')
<!-- Navbar Landing -->
<nav class="landing-navbar">
    <div class="navbar-container">
        <div class="navbar-brand">
            <a href="#" class="brand-logo">
                <span class="brand-text">ASTROLAB</span>
            </a>
        </div>
        
        <div class="navbar-menu" id="navbar-menu">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a href="#boutique" class="nav-link">COLLECTIONS</a>
                </li>
                <li class="nav-item">
                    <a href="#illustrateurs" class="nav-link">ILLUSTRATEURS</a>
                </li>
                <li class="nav-item">
                    <a href="#contact" class="nav-link">CONTACT</a>
                </li>
                <li class="nav-item">
                    <a href="#newsletter" class="nav-link">NEWSLETTER</a>
                </li>
            </ul>
        </div>
        
        <div class="navbar-actions">
            <a href="#contact" class="navbar-cta">
                NOUS CONTACTER
            </a>
        </div>
        
        <!-- Menu burger pour mobile -->
        <button class="mobile-menu-toggle" id="mobile-menu-toggle">
            <span class="burger-line"></span>
            <span class="burger-line"></span>
            <span class="burger-line"></span>
        </button>
    </div>
</nav>

<div class="landing-container">
    
    <!-- Hero Section avec bannière -->
    <section class="bg-home hero-section" style="height: 70vh; display: flex; flex-direction: column;">
        <div class="hero-banner" style="flex: 1; background-image: url('/assets/img/BANIERE-COLLECTION-SITE-ASTROLAB.webp'); background-size: cover; background-position: center; background-repeat: no-repeat; position: relative; display: flex; align-items: center; justify-content: center;">
            <div class="hero-overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0, 0, 0, 0.4);"></div>
        </div>
        <div class="hero-content" style="background: rgba(34, 34, 34, 0.95); padding: 40px 20px; text-align: center; position: relative; z-index: 2;">
            <div class="hero-text">
                <p class="hero-subtitle" style="font-size: 1.2rem; font-weight: 400; letter-spacing: 2px; color: rgba(255,255,255,0.9); margin: 0; text-shadow: 2px 2px 4px rgba(0,0,0,0.5); border-left: 2px solid white; border-right: 2px solid white; padding: 0 20px; display: inline-block;">
                    DES ÉDITIONS ÉPHÉMÈRES EXCLUSIVES IMAGINÉES PAR DES ILLUSTRATEURS INDÉPENDANTS
                </p>
            </div>
        </div>
    </section>
    
    <!-- Séparateur lumineux -->
    <div class="divider"></div>
    
    <!-- Section Boutique -->
    <section id="boutique" class="section-container text-center">
        <div class="max-width">
            <h2 class="section-title">COLLECTION STELLAE 01</h2>
            <p class="section-subtitle">DÉCOUVREZ NOTRE PREMIÈRE COLLECTION EXCLUSIVE</p>
            
            <!-- Galerie de produits en grid 3x2 -->
            <div class="products-grid" style="display: grid; grid-template-columns: 1fr 1fr 1fr; grid-template-rows: auto auto; gap: 20px; max-width: 1200px; margin: 0 auto;">
                <!-- Ligne du haut : 3 produits -->
                <div class="card product-card">
                    <div class="product-flip-container" data-card-id="tshirt-classique">
                        <div class="product-card-inner">
                            <!-- Face avant -->
                            <div class="product-face product-front">
                                <img src="/assets/img/sweat-blanc.jpeg" alt="T-Shirt Classique" class="product-image" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div class="product-placeholder" style="display: none;">T-SHIRT CLASSIQUE</div>
                            </div>
                            <!-- Face arrière -->
                            <div class="product-face product-back">
                                <img src="/assets/img/sweat-noir.jpeg" alt="T-Shirt Classique Dos" class="product-image" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div class="product-placeholder" style="display: none;">T-SHIRT DOS</div>
                            </div>
                        </div>
                        <!-- Zones de clic invisibles -->
                        <div class="click-zone click-left" onclick="flipCard(this, 'left')"></div>
                        <div class="click-zone click-right" onclick="flipCard(this, 'right')"></div>
                    </div>
                    <h3 class="card-title">T-SHIRT CLASSIQUE "STELLAE 01"</h3>
                    <p class="card-text">Coupe classique • Noir ou Blanc</p>
                </div>
                
                <div class="card product-card">
                    <div class="product-flip-container" data-card-id="tshirt-oversize">
                        <div class="product-card-inner">
                            <!-- Face avant -->
                            <div class="product-face product-front">
                                <img src="/assets/img/sweat-blanc.jpeg" alt="T-Shirt Oversize" class="product-image" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div class="product-placeholder" style="display: none;">T-SHIRT OVERSIZE</div>
                            </div>
                            <!-- Face arrière -->
                            <div class="product-face product-back">
                                <img src="/assets/img/sweat-noir.jpeg" alt="T-Shirt Oversize Dos" class="product-image" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div class="product-placeholder" style="display: none;">T-SHIRT DOS</div>
                            </div>
                        </div>
                        <!-- Zones de clic invisibles -->
                        <div class="click-zone click-left" onclick="flipCard(this, 'left')"></div>
                        <div class="click-zone click-right" onclick="flipCard(this, 'right')"></div>
                    </div>
                    <h3 class="card-title">T-SHIRT OVERSIZE "STELLAE 01"</h3>
                    <p class="card-text">Coupe oversize • Noir ou Blanc</p>
                </div>
                
                <div class="card product-card">
                    <div class="product-flip-container" data-card-id="hoodie">
                        <div class="product-card-inner">
                            <!-- Face avant -->
                            <div class="product-face product-front">
                                <img src="/assets/img/sweat-blanc.jpeg" alt="Hoodie" class="product-image" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div class="product-placeholder" style="display: none;">HOODIE</div>
                            </div>
                            <!-- Face arrière -->
                            <div class="product-face product-back">
                                <img src="/assets/img/sweat-noir.jpeg" alt="Hoodie Dos" class="product-image" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div class="product-placeholder" style="display: none;">HOODIE DOS</div>
                            </div>
                        </div>
                        <!-- Zones de clic invisibles -->
                        <div class="click-zone click-left" onclick="flipCard(this, 'left')"></div>
                        <div class="click-zone click-right" onclick="flipCard(this, 'right')"></div>
                    </div>
                    <h3 class="card-title">HOODIE "STELLAE 01"</h3>
                    <p class="card-text">Capuche doublée • Noir ou Blanc</p>
                </div>

                <!-- Ligne du bas : 1 produit + carousel large -->
                <div class="card product-card">
                    <div class="product-flip-container" data-card-id="sweatshirt">
                        <div class="product-card-inner">
                            <!-- Face avant -->
                            <div class="product-face product-front">
                                <img src="/assets/img/sweat-blanc.jpeg" alt="Sweatshirt" class="product-image" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div class="product-placeholder" style="display: none;">SWEATSHIRT</div>
                            </div>
                            <!-- Face arrière -->
                            <div class="product-face product-back">
                                <img src="/assets/img/sweat-noir.jpeg" alt="Sweatshirt Dos" class="product-image" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div class="product-placeholder" style="display: none;">SWEATSHIRT DOS</div>
                            </div>
                        </div>
                        <!-- Zones de clic invisibles -->
                        <div class="click-zone click-left" onclick="flipCard(this, 'left')"></div>
                        <div class="click-zone click-right" onclick="flipCard(this, 'right')"></div>
                    </div>
                    <h3 class="card-title">SWEATSHIRT "STELLAE 01"</h3>
                    <p class="card-text">Col rond • Noir ou Blanc</p>
                </div>

                <!-- Collection showcase qui prend 2 colonnes -->
                <div class="collection-showcase-large card" style="grid-column: span 2;">
                    <div class="collection-grid" style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px; margin-bottom: 16px;">
                        <!-- Image 1 -->
                        <div class="collection-item" style="position: relative; aspect-ratio: 1; overflow: hidden; border-radius: 8px;">
                            <img src="/assets/img/sweat-blanc.jpeg" alt="Collection Stellae 01 - T-Shirt Blanc" style="width: 100%; height: 100%; object-fit: cover; object-position: center; transition: transform 0.3s ease;">
                        </div>
                        <!-- Image 2 -->
                        <div class="collection-item" style="position: relative; aspect-ratio: 1; overflow: hidden; border-radius: 8px;">
                            <img src="/assets/img/sweat-noir.jpeg" alt="Collection Stellae 01 - T-Shirt Noir" style="width: 100%; height: 100%; object-fit: cover; object-position: center; transition: transform 0.3s ease;">
                        </div>
                        <!-- Image 3 -->
                        <div class="collection-item" style="position: relative; aspect-ratio: 1; overflow: hidden; border-radius: 8px;">
                            <img src="/assets/img/BANIERE-COLLECTION-SITE-ASTROLAB.webp" alt="Collection Stellae 01 - Bannière" style="width: 100%; height: 100%; object-fit: cover; object-position: center; transition: transform 0.3s ease;">
                        </div>
                    </div>
                    
                    <!-- Texte dans le fond gris comme les autres cartes -->
                    <h3 class="card-title">DÉCOUVRIR LA COLLECTION "STELLAE"</h3>
                    <p class="card-text">T-SHIRT | T-SHIRT OVERSIZE | HOODIE</p>
                </div>
            </div>            <!-- Call to Action -->
            <div class="text-center space-y">
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
                    {{-- <a href="#newsletter" class="btn-home">
                        RECEVOIR LES ACTUALITÉS
                    </a> --}}
                </div>
            </div>
        </div>
    </section>
    
    <!-- Section Illustrateurs -->
    <section id="illustrateurs" class="section-container">
        <div class="max-width text-center">
            <h2 class="section-title">ILLUSTRATEURS - GRAPHISTES</h2>
            <h3 class="section-subtitle">| SUIVEZ MATHIS DOUILLARD & AXEL CHAPET SUR LEURS RÉSEAUX POUR DÉCOUVRIR LEUR UNIVERS |</h3>
            
            <div class="grid-3" style="grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));">
                <div class="card">
                    <div class="card-icon" style="width: 120px; height: 120px; background-color: #666; border-radius: 50%; margin: 0 auto 24px;"></div>
                    <h4 class="card-title">MATHIS DOUILLARD</h4>
                    <a href="#" class="btn-home">SUIVRE MATHIS</a>
                </div>
                <div class="card">
                    <div class="card-icon" style="width: 120px; height: 120px; background-color: #666; border-radius: 50%; margin: 0 auto 24px;"></div>
                    <h4 class="card-title">AXEL CHAPET</h4>
                    <a href="#" class="btn-home">SUIVRE AXEL</a>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Section Newsletter -->
    <section id="newsletter" class="section-container footer-section">
        <div class="max-width text-center">
            <h2 class="section-title">RESTEZ INFORMÉ</h2>
            <p class="section-subtitle">Soyez le premier à découvrir nos nouvelles collections et bénéficiez d'offres exclusives</p>
            <div class="newsletter-container">
                <input type="email" placeholder="Votre adresse email" class="form-input newsletter-form" style="width: 400px;">
                <button class="btn-home">
                    S'ABONNER
                </button>
            </div>
        </div>
    </section>
    
    <!-- Section Contact -->
    <section id="contact" class="section-container">
        <div class="max-width">
            <div class="text-center space-y">
                <h2 class="section-title">CONTACT</h2>
                <h3 class="section-subtitle">| GILDAS CHAUVEL - FONDATEUR D'ASTROLAB |</h3>
            </div>
            
            <div class="grid-3" style="grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));">
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
                                <p>gildas@astrolab.fr</p>
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
                        <a href="mailto:gildas@astrolab.fr" class="btn-home">
                            ENVOYER UN EMAIL
                        </a>
                    </div>
                </div>
                
                <!-- Formulaire de contact -->
                <div class="form-container">
                    <h4 class="card-title">ENVOYEZ-NOUS UN MESSAGE</h4>
                    <form action="https://api.web3forms.com/submit" method="POST">
                        <!-- Configuration Web3Forms -->
                        <input type="hidden" name="access_key" value="VOTRE_ACCESS_KEY_ICI">
                        <input type="hidden" name="subject" value="Nouveau message depuis ASTROLAB">
                        <input type="hidden" name="from_name" value="Site ASTROLAB">
                        <input type="hidden" name="redirect" value="{{ url('/merci') }}">
                        
                        <!-- Protection anti-spam -->
                        <input type="checkbox" name="botcheck" style="display: none;">
                        
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
    
    <!-- Footer -->
    <footer class="footer-section">
        <div class="max-width text-center">
            <div class="space-y">
                <h3 class="section-title">ASTROLAB</h3>
                <p class="card-text">Créations exclusives • Éditions limitées • Qualité premium</p>
            </div>
            
            <div class="grid-3">
                <div>
                    <h4 class="card-title">LIENS RAPIDES</h4>
                    <div class="card-text">
                        <a href="#boutique" class="card-text">Boutique</a><br>
                        <a href="#illustrateurs" class="card-text">Illustrateurs</a><br>
                        <a href="#contact" class="card-text">Contact</a>
                    </div>
                </div>
                <div>
                    <h4 class="card-title">INFORMATIONS</h4>
                    <div class="card-text">
                        Éditions limitées<br>
                        Livraison France<br>
                        Qualité premium
                    </div>
                </div>
                <div>
                    <h4 class="card-title">SUIVEZ-NOUS</h4>
                    <div class="card-text">
                        <a href="https://instagram.com/" target="_blank" style="color: rgba(255,255,255,0.8); font-size: 32px; transition: all 0.3s ease;">
                            <i class="fab fa-instagram"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <div style="border-top: 1px solid rgba(255,255,255,0.2); padding-top: 32px; margin-top: 32px;">
                <p class="card-text">
                    © 2025 ASTROLAB - Tous droits réservés | 
                    Créé par <a href="https://ec-craft.fr" target="_blank" style="color: rgba(255,255,255,0.7);">ec-craft.fr</a>
                </p>
            </div>
        </div>
    </footer>
</div>

<!-- JavaScript pour le flip des cartes -->
<script>
// === STOCKAGE DES ÉTATS ET TIMEOUTS DES CARTES ===
const cardStates = new Map();

// Fonction pour faire tourner les cartes produits
function flipCard(clickZone, direction) {
    const container = clickZone.closest('.product-flip-container');
    const cardInner = container.querySelector('.product-card-inner');
    const cardId = container.getAttribute('data-card-id') || container.closest('.product-card').querySelector('.card-title').textContent;
    
    // Vérifier si la carte est déjà en cours de flip
    if (cardStates.has(cardId) && cardStates.get(cardId).isFlipping) {
        return; // Ignorer le clic si la carte est déjà en cours de flip
    }
    
    // Annuler le timeout précédent s'il existe
    if (cardStates.has(cardId) && cardStates.get(cardId).timeout) {
        clearTimeout(cardStates.get(cardId).timeout);
    }
    
    // Déterminer l'état actuel de la carte
    const currentState = cardStates.get(cardId) || { isFlipped: false, direction: null };
    
    // Si la carte est déjà flippée, la remettre à l'état initial
    if (currentState.isFlipped) {
        cardInner.classList.remove('flipped-left', 'flipped-right');
        cardStates.set(cardId, { isFlipped: false, direction: null, isFlipping: false });
        return;
    }
    
    // Marquer la carte comme en cours de flip
    cardStates.set(cardId, { 
        isFlipped: false, 
        direction: null, 
        isFlipping: true,
        timeout: null 
    });
    
    // Retirer toutes les classes de flip existantes
    cardInner.classList.remove('flipped-left', 'flipped-right');
    
    // Petite pause pour permettre la transition
    setTimeout(() => {
        // Ajouter la classe appropriée selon la direction
        if (direction === 'left') {
            cardInner.classList.add('flipped-left');
        } else if (direction === 'right') {
            cardInner.classList.add('flipped-right');
        }
        
        // Mettre à jour l'état
        const timeout = setTimeout(() => {
            cardInner.classList.remove('flipped-left', 'flipped-right');
            cardStates.set(cardId, { isFlipped: false, direction: null, isFlipping: false });
        }, 3000);
        
        cardStates.set(cardId, { 
            isFlipped: true, 
            direction: direction, 
            isFlipping: false,
            timeout: timeout 
        });
    }, 50);
}

// === EFFET DE TYPING POUR LE TITRE ===
function typeWriter(element, text, speed = 100) {
    let i = 0;
    element.innerHTML = '';
    
    function typing() {
        if (i < text.length) {
            element.innerHTML += text.charAt(i);
            i++;
            setTimeout(typing, speed);
        }
    }
    typing();
}

// === INITIALISATION AU CHARGEMENT DE LA PAGE ===
document.addEventListener('DOMContentLoaded', function() {
    // === EFFETS HOVER POUR LES IMAGES DE COLLECTION ===
    const collectionItems = document.querySelectorAll('.collection-item img');
    collectionItems.forEach(img => {
        img.addEventListener('mouseenter', () => {
            img.style.transform = 'scale(1.05)';
        });
        
        img.addEventListener('mouseleave', () => {
            img.style.transform = 'scale(1)';
        });
    });

    // === NAVBAR MOBILE ===
    const mobileToggle = document.getElementById('mobile-menu-toggle');
    const navbarMenu = document.getElementById('navbar-menu');
    
    if (mobileToggle && navbarMenu) {
        mobileToggle.addEventListener('click', function() {
            mobileToggle.classList.toggle('active');
            navbarMenu.classList.toggle('active');
        });
    }
    
    // Fermer le menu au clic sur un lien
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
        link.addEventListener('click', () => {
            if (mobileToggle && navbarMenu) {
                mobileToggle.classList.remove('active');
                navbarMenu.classList.remove('active');
            }
        });
    });

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

// === NAVBAR SCROLL EFFECT ===
window.addEventListener('scroll', function() {
    const navbar = document.querySelector('.landing-navbar');
    if (window.scrollY > 50) {
        navbar.classList.add('scrolled');
    } else {
        navbar.classList.remove('scrolled');
    }
});

// === EFFET DE TYPING AU CHARGEMENT DE LA PAGE ===
window.addEventListener('load', () => {
    const heroSubtitle = document.querySelector('.hero-subtitle');
    if (heroSubtitle) {
        const originalText = heroSubtitle.textContent;
        typeWriter(heroSubtitle, originalText, 50);
    }
});
</script>

@endsection
