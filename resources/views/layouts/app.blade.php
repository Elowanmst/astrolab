<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Astrolab</title>
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/modal.js', 'resources/js/size.js'])
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
                    <a href="{{ route('cart.index') }}" class="hover:text-gray-300 uppercase">
                        Panier ({{ $cart->count() }})
                    </a>
                </li>
                @auth
                    <li>
                        <a href="/admin" class="hover:text-gray-300 uppercase">
                            Admin
                        </a>
                    </li>
                @endauth
            </div>
        </ul>
    </nav>
    <main>@yield('content')</main>
</body>
</html>