<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>Astrolab</title>  

    {{-- icônes --}}
    <link rel="icon" type="image/svg+xml" href="/favicon.svg" />
    <link rel="icon" type="image/png" href="/favicon-96x96.png" sizes="96x96" />
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-title" content="Astrolab" />
    <link rel="manifest" href="/site.webmanifest" />
    <link rel="shortcut icon" href="/favicon.ico" />

    
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/modal.js', 'resources/js/size.js'])


    
    <!-- Styles et scripts spécifiques aux pages produit -->
    @if(request()->routeIs('products.show'))
        @vite(['resources/css/product-detail.css', 'resources/js/product-detail.js'])
    @endif
    
    <!-- Styles spécifiques aux pages checkout -->
    @if(request()->routeIs('checkout.*'))
        @vite(['resources/css/checkout.css'])
    @endif
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
                    
                    <!-- Lien panier pour mobile -->
                    <li class="nav-item mobile-cart-item">
                        <a href="{{ route('cart.index') }}" class="nav-link mobile-cart-link">
                            <i class="fas fa-shopping-bag"></i>
                            Panier
                            @inject('cart', 'App\Services\Cart')
                            @if($cart->count() > 0)
                                <span class="mobile-cart-badge">{{ $cart->count() }}</span>
                            @endif
                        </a>
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
    
    <!-- Footer -->
    @include('partials.footer')
    
    <!-- Popup ajout au panier -->
    @if(session('cart_success'))
    <div id="cart-popup" class="cart-popup-overlay">
        <div class="cart-popup">
            <div class="cart-popup-content">
                <i class="fas fa-check-circle cart-popup-icon"></i>
                <h3>{{ session('cart_success')['message'] }}</h3>
                <p><strong>{{ session('cart_success')['product_name'] }}</strong> a été ajouté à votre panier</p>
                
                <div class="cart-popup-buttons">
                    <a href="{{ route('home') }}" class="btn-glass btn-glass-secondary">
                        <i class="fas fa-arrow-left"></i> Continuer mes achats
                    </a>
                    <a href="{{ route('cart.index') }}" class="btn-glass btn-glass-primary">
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