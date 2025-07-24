<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Astrolab</title>
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/modal.js', 'resources/js/size.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <nav class="bg-[rgb(34,34,34)] text-white p-4 ">
        <ul class="flex justify-between items-center">
            <div class="flex space-x-4">
                @auth
                    <li><a href="{{ route('home') }}" class="hover:text-gray-300 uppercase">Accueil</a></li>
                    <li><a href="" class="hover:text-gray-300 uppercase">Boutique</a></li>
                    <li><a href="" class="hover:text-gray-300 uppercase">Contact</a></li>
                @else
                    <li><a href="{{ route('home') }}" class="hover:text-gray-300 uppercase">Accueil</a></li>
                    <li><a href="" class="hover:text-gray-300 uppercase">Boutique</a></li>
                    <li><a href="" class="hover:text-gray-300 uppercase">Contact</a></li>
                @endauth
            </div>
            <div class="flex items-center space-x-4">
                @inject('cart', 'App\Services\Cart')

                <li>
                    <a href="{{ route('cart.index') }}" class="hover:text-gray-300 relative inline-block">
                        <i class="fas fa-shopping-bag text-lg"></i>
                        @if($cart->count() > 0)
                            <span class="absolute -top-2 -right-2 bg-black text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-bold border border-white">
                                {{ $cart->count() }}
                            </span>
                        @endif
                    </a>
                </li>
                @auth
                    <li>
                        <a href="{{ route('profile') }}" class="hover:text-gray-300 uppercase">
                            {{ Auth::user()->name }}
                        </a>
                    </li>
                    @if(str_ends_with(Auth::user()->email, '@astrolab.com'))
                        <li>
                            <a href="/admin" class="hover:text-gray-300 uppercase">
                                Admin
                            </a>
                        </li>
                    @endif
                    <li>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="hover:text-gray-300 uppercase bg-transparent border-none text-white cursor-pointer">
                                Déconnexion
                            </button>
                        </form>
                    </li>
                @else
                    <li>
                        <a href="{{ route('login') }}" class="hover:text-gray-300 uppercase">
                            Se connecter
                        </a>
                    </li>
                @endauth
            </div>
        </ul>
    </nav>
    <main>@yield('content')</main>
    
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
</body>
</html>