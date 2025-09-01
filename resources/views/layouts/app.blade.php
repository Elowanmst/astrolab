<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Astrolab</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <nav class="landing-navbar">
        <div class="navbar-container">
            <!-- Brand/Logo -->
            <div class="navbar-brand">
                <a href="{{ route('home') }}" class="brand-text">ASTROLAB</a>
            </div>
            
            <!-- Navigation Menu -->
            <div class="navbar-menu">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a href="{{ route('home') }}" class="nav-link">Accueil</a>
                    </li>
                    <li class="nav-item">
                        <a href="#boutique" class="nav-link">Boutique</a>
                    </li>
                    <li class="nav-item">
                        <a href="#contact" class="nav-link">Contact</a>
                    </li>
                    @if(str_ends_with(Auth::user()->email ?? '', '@astrolab.com'))
                        <li class="nav-item">
                            <a href="/admin" class="nav-link" target="_blank">Admin</a>
                        </li>
                    @endif
                </ul>
            </div>
            
            <!-- Actions (Cart, Profile, Login) -->
            <div class="navbar-actions">
                @inject('cart', 'App\Services\Cart')
                
                <!-- Cart Icon -->
                <a href="{{ route('cart.index') }}" class="navbar-cart-icon">
                    <i class="fas fa-shopping-bag"></i>
                    @if($cart->count() > 0)
                        <span class="cart-badge">{{ $cart->count() }}</span>
                    @endif
                </a>
                
                @auth
                    <!-- User Profile -->
                    <div class="navbar-user-menu">
                        <a href="{{ route('profile') }}" class="nav-link">{{ Auth::user()->name }}</a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="navbar-logout-btn">
                                Déconnexion
                            </button>
                        </form>
                    </div>
                @else
                    <!-- Login Button -->
                    <a href="{{ route('login') }}" class="navbar-cta">Se connecter</a>
                @endauth
            </div>
            
            <!-- Mobile Menu Toggle -->
            <button class="mobile-menu-toggle">
                <div class="burger-line"></div>
                <div class="burger-line"></div>
                <div class="burger-line"></div>
            </button>
        </div>
    </nav>
    
    <main style="padding-top: 70px;">@yield('content')</main>
    
    <!-- Popup ajout au panier -->
    @if(session('cart_success'))
    <div id="cart-popup" class="cart-popup-overlay">
        <div class="cart-popup">
            <div class="cart-popup-content">
                <i class="fas fa-check-circle cart-popup-icon"></i>
                <h3>{{ session('cart_success')['message'] }}</h3>
                <p><strong>{{ session('cart_success')['product_name'] }}</strong> a été ajouté à votre panier</p>
                
                <div class="cart-popup-buttons">
                    <a href="{{ route('home') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Continuer mes achats
                    </a>
                    <a href="{{ route('cart.index') }}" class="btn btn-primary">
                        <i class="fas fa-shopping-cart"></i> Voir mon panier
                    </a>
                </div>
                
                <button class="cart-popup-close" onclick="closeCartPopup()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </div>

    <script>
    function closeCartPopup() {
        document.getElementById('cart-popup').style.display = 'none';
    }

    // Fermer en cliquant à l'extérieur
    document.getElementById('cart-popup').addEventListener('click', function(e) {
        if (e.target === this) closeCartPopup();
    });

    // Fermer avec Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeCartPopup();
    });
    </script>
    @endif
    
    <!-- Script pour la navbar responsive -->
    <script>
    // Toggle mobile menu
    document.addEventListener('DOMContentLoaded', function() {
        const mobileToggle = document.querySelector('.mobile-menu-toggle');
        const navbarMenu = document.querySelector('.navbar-menu');
        
        if (mobileToggle && navbarMenu) {
            mobileToggle.addEventListener('click', function() {
                mobileToggle.classList.toggle('active');
                navbarMenu.classList.toggle('active');
            });
        }
        
        // Navbar scroll effect
        const navbar = document.querySelector('.landing-navbar');
        if (navbar) {
            window.addEventListener('scroll', function() {
                if (window.scrollY > 50) {
                    navbar.classList.add('scrolled');
                } else {
                    navbar.classList.remove('scrolled');
                }
            });
        }
    });
    </script>
</body>
</html>