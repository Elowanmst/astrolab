@extends('layouts.app')

@section('content')
<div class="landing-container">
    
    <!-- Hero Section avec bannière -->
    <section class="bg-home hero-section">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <div class="hero-text">
                <h1 class="hero-title">
                    ASTROLABe
                </h1>
                <p class="hero-subtitle">
                    DES ÉDITIONS ÉPHÉMÈRES EXCLUSIVES IMAGINÉES PAR DES ILLUSTRATEURS INDÉPENDANTS
                </p>
                <div class="space-y">
                    <a href="#boutique" class="btn-home">
                        DÉCOUVRIR LA COLLECTION
                    </a>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Séparateur lumineux -->
    <div class="divider"></div>
    
    <!-- Section À propos -->
    <section class="section-container">
        <div class="max-width">
            <h2 class="section-title">POURQUOI ASTROLAB ?</h2>
            <div class="grid-3">
                <div class="card">
                    <div class="card-icon">🎨</div>
                    <h3 class="card-title">CRÉATIONS EXCLUSIVES</h3>
                    <p class="card-text">Chaque design est unique et créé en collaboration avec des artistes indépendants talentueux.</p>
                </div>
                <div class="card">
                    <div class="card-icon">⏰</div>
                    <h3 class="card-title">ÉDITIONS LIMITÉES</h3>
                    <p class="card-text">Nos collections sont éphémères. Une fois épuisées, elles ne reviendront plus jamais.</p>
                </div>
                <div class="card">
                    <div class="card-icon">⭐</div>
                    <h3 class="card-title">QUALITÉ PREMIUM</h3>
                    <p class="card-text">Matériaux de haute qualité et finitions soignées pour des pièces qui durent.</p>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Section Boutique avec compte à rebours -->
    <section id="boutique" class="section-container text-center">
        <div class="max-width">
            <h2 class="section-title">BOUTIQUE</h2>
            <p class="section-subtitle">FIN DES PRÉCOMMANDES DANS...</p>
            
            <!-- Compteur à rebours stylisé -->
            <div class="countdown-container">
                <div class="countdown-item">
                    <span class="countdown-number" id="days">00</span>
                    <span class="countdown-label">JOURS</span>
                </div>
                <div class="countdown-separator">:</div>
                <div class="countdown-item">
                    <span class="countdown-number" id="hours">00</span>
                    <span class="countdown-label">HEURES</span>
                </div>
                <div class="countdown-separator">:</div>
                <div class="countdown-item">
                    <span class="countdown-number" id="minutes">00</span>
                    <span class="countdown-label">MINUTES</span>
                </div>
                <div class="countdown-separator">:</div>
                <div class="countdown-item">
                    <span class="countdown-number" id="seconds">00</span>
                    <span class="countdown-label">SECONDES</span>
                </div>
            </div>
            
            <!-- Galerie de produits -->
            <div class="grid-3 space-y">
                <div class="card">
                    <div class="card-icon" style="height: 200px; background-color: #666; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #999;">
                        T-SHIRT ASTROLAB
                    </div>
                    <h3 class="card-title">T-SHIRT OVERSIZE ASTROLAB "STELLAE 01" NOIR OU BLANC</h3>
                    <p class="card-text">À partir de 35€</p>
                </div>
                <div class="card">
                    <div class="card-icon" style="height: 200px; background-color: #666; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #999;">
                        HOODIE ASTROLAB
                    </div>
                    <h3 class="card-title">HOODIE ASTROLAB "STELLAE 01" NOIR OU BLANC</h3>
                    <p class="card-text">À partir de 65€</p>
                </div>
                <div class="card">
                    <div class="card-icon" style="height: 200px; background-color: #666; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #999;">
                        COLLECTION COMPLÈTE
                    </div>
                    <h3 class="card-title">COLLECTION STELLAE 01 COMPLÈTE</h3>
                    <p class="card-text">Découvrez tous nos designs</p>
                </div>
            </div>
            
            <!-- Call to Action principal -->
            <div class="text-center space-y">
                <a href="#contact" class="btn-home">
                    PRÉCOMMANDER MAINTENANT
                </a>
                <div class="button-container">
                    <a href="#contact" class="btn-home">
                        NOUS CONTACTER
                    </a>
                    <a href="#illustrateurs" class="btn-home">
                        DÉCOUVRIR LES ARTISTES
                    </a>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Section Témoignages -->
    <section class="section-container footer-section">
        <div class="max-width text-center">
            <h2 class="section-title">CE QUE DISENT NOS CLIENTS</h2>
            <div class="grid-3" style="grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));">
                <div class="card">
                    <div class="card-icon">⭐⭐⭐⭐⭐</div>
                    <p class="card-text">"Qualité exceptionnelle et designs uniques. Je recommande vivement ASTROLAB !"</p>
                    <p class="card-text">- Marine L.</p>
                </div>
                <div class="card">
                    <div class="card-icon">⭐⭐⭐⭐⭐</div>
                    <p class="card-text">"Concept génial ! J'adore porter des pièces que personne d'autre n'a."</p>
                    <p class="card-text">- Thomas K.</p>
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
                    <p class="card-text">Illustrateur passionné par l'univers spatial et les formes géométriques. Ses créations mélangent science et art avec une touche moderne.</p>
                    <a href="#" class="btn-home">SUIVRE MATHIS</a>
                </div>
                <div class="card">
                    <div class="card-icon" style="width: 120px; height: 120px; background-color: #666; border-radius: 50%; margin: 0 auto 24px;"></div>
                    <h4 class="card-title">AXEL CHAPET</h4>
                    <p class="card-text">Graphiste expert en typographie et design minimaliste. Il apporte une dimension artistique unique à chaque création ASTROLAB.</p>
                    <a href="#" class="btn-home">SUIVRE AXEL</a>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Section Newsletter -->
    <section class="section-container footer-section">
        <div class="max-width text-center">
            <h2 class="section-title">RESTEZ INFORMÉ</h2>
            <p class="section-subtitle">Soyez le premier à découvrir nos nouvelles collections et bénéficiez d'offres exclusives</p>
            <div class="button-container">
                <input type="email" placeholder="Votre adresse email" class="form-input" style="width: 400px;">
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
                        <div class="space-y">
                            <div style="display: flex; align-items: center; gap: 16px;">
                                <i class="fas fa-phone" style="font-size: 24px; color: #3b82f6;"></i>
                                <div>
                                    <p style="color: #9ca3af; font-size: 14px;">Téléphone</p>
                                    <p style="color: white; font-size: 18px;">06 00 00 00 00</p>
                                </div>
                            </div>
                            <div style="display: flex; align-items: center; gap: 16px;">
                                <i class="fas fa-envelope" style="font-size: 24px; color: #3b82f6;"></i>
                                <div>
                                    <p style="color: #9ca3af; font-size: 14px;">Email</p>
                                    <p style="color: white; font-size: 18px;">gildas@astrolab.fr</p>
                                </div>
                            </div>
                            <div style="display: flex; align-items: center; gap: 16px;">
                                <i class="fab fa-instagram" style="font-size: 24px; color: #ec4899;"></i>
                                <div>
                                    <p style="color: #9ca3af; font-size: 14px;">Instagram</p>
                                    <a href="https://instagram.com/" target="_blank" style="color: white; font-size: 18px;">
                                        @astrolab_official
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- CTA urgence -->
                    <div class="card" style="background: linear-gradient(to right, #3b82f6, #8b5cf6); margin-top: 24px;">
                        <h4 class="card-title">ENVIE DE PRÉCOMMANDER ?</h4>
                        <p class="card-text">Contactez-nous dès maintenant pour réserver votre pièce avant la fin de la collection</p>
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
                        <a href="#illustrateurs" class="card-text">Artistes</a><br>
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
                        <a href="https://instagram.com/" target="_blank" style="color: #ec4899; font-size: 32px;">
                            <i class="fab fa-instagram"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <div style="border-top: 1px solid rgba(255,255,255,0.2); padding-top: 32px; margin-top: 32px;">
                <p class="card-text">
                    © 2025 ASTROLAB - Tous droits réservés | 
                    Créé par <a href="https://ec-craft.fr" target="_blank" style="color: #3b82f6;">ec-craft.fr</a>
                </p>
            </div>
        </div>
    </footer>
</div>

<!-- JavaScript pour le compteur -->
<script>
// Countdown timer
function updateCountdown() {
    // Date cible (ajustez selon vos besoins)
    const targetDate = new Date('2025-08-01T00:00:00').getTime();
    const now = new Date().getTime();
    const difference = targetDate - now;
    
    if (difference > 0) {
        const days = Math.floor(difference / (1000 * 60 * 60 * 24));
        const hours = Math.floor((difference % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((difference % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((difference % (1000 * 60)) / 1000);
        
        document.getElementById('days').textContent = days.toString().padStart(2, '0');
        document.getElementById('hours').textContent = hours.toString().padStart(2, '0');
        document.getElementById('minutes').textContent = minutes.toString().padStart(2, '0');
        document.getElementById('seconds').textContent = seconds.toString().padStart(2, '0');
    }
}

// Mettre à jour le compteur chaque seconde
setInterval(updateCountdown, 1000);
updateCountdown();

// Smooth scroll pour les liens d'ancre
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
</script>

@endsection
