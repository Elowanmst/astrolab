@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-black text-white py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold">ASTROLAB</h1>
            <h2 class="mt-6 text-2xl">FINALISER MA COMMANDE</h2>
            <p class="mt-2 text-gray-400">| ÉTAPE 1/3 : IDENTIFICATION |</p>
        </div>
        
        <div class="w-full h-1 bg-white shadow-[0_0_10px_2px_rgba(255,255,255,0.7)] mx-auto mb-12"></div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Résumé du panier -->
            <div class="lg:col-span-1 bg-gray-900 p-6 border border-gray-700">
                <h3 class="text-xl font-semibold mb-6 uppercase">Mon panier</h3>
                
                @foreach($cart->get() as $item)
                    <div class="flex items-center space-x-4 mb-4 pb-4 border-b border-gray-700">
                        @if($item['image'])
                            <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="w-16 h-16 object-cover">
                        @else
                            <div class="w-16 h-16 bg-gray-600 flex items-center justify-center">
                                <span class="text-gray-400 text-xs">NO IMG</span>
                            </div>
                        @endif
                        <div class="flex-1">
                            <h4 class="font-medium">{{ $item['name'] }}</h4>
                            @if($item['size'])
                                <p class="text-sm text-gray-400">Taille: {{ $item['size'] }}</p>
                            @endif
                            @if($item['color'])
                                <p class="text-sm text-gray-400">Couleur: {{ $item['color'] }}</p>
                            @endif
                            <p class="text-sm">{{ $item['quantity'] }} x {{ $item['price'] }}€</p>
                        </div>
                    </div>
                @endforeach
                
                <div class="mt-6 space-y-2">
                    <div class="flex justify-between">
                        <span>Total HT:</span>
                        <span>{{ number_format($cart->getTotalHT(), 2) }}€</span>
                    </div>
                    <div class="flex justify-between">
                        <span>TVA (20%):</span>
                        <span>{{ number_format($cart->getTVA(), 2) }}€</span>
                    </div>
                    <div class="flex justify-between font-bold text-lg">
                        <span>Total TTC:</span>
                        <span>{{ number_format($cart->getTotalTTC(), 2) }}€</span>
                    </div>
                    <p class="text-sm text-gray-400">+ Frais de livraison (calculés à l'étape suivante)</p>
                </div>
            </div>

            <!-- Options de connexion -->
            <div class="lg:col-span-2 space-y-8">
                
                @auth
                    <!-- Utilisateur déjà connecté -->
                    <div class="bg-green-900 p-6 border border-green-700">
                        <h3 class="text-xl font-semibold mb-4 uppercase">Connecté en tant que</h3>
                        <p class="text-green-300 mb-4">{{ Auth::user()->name }} ({{ Auth::user()->email }})</p>
                        
                        <form action="{{ route('checkout.shipping') }}" method="GET">
                            <button type="submit" 
                                    class="w-full py-3 px-4 border border-green-400 text-sm font-medium uppercase text-green-400 bg-transparent hover:bg-green-400 hover:text-black transition-colors duration-200">
                                Continuer avec ce compte
                            </button>
                        </form>
                    </div>
                @else
                    <!-- Créer un compte -->
                    <div class="bg-gray-900 p-6 border border-gray-700">
                        <h3 class="text-xl font-semibold mb-4 uppercase">Créer un compte</h3>
                        <p class="text-gray-400 mb-6">Créez un compte pour suivre vos commandes et bénéficier d'avantages exclusifs.</p>
                        
                        <form action="{{ route('checkout.shipping') }}" method="POST" class="space-y-4">
                            @csrf
                            <input type="hidden" name="checkout_type" value="register">
                            
                            <div>
                                <label for="register_name" class="block text-sm font-medium text-gray-300 uppercase">Nom complet</label>
                                <input type="text" name="register_name" id="register_name" required 
                                       class="mt-1 block w-full px-3 py-2 bg-gray-800 border border-gray-600 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-white focus:border-transparent">
                            </div>
                            
                            <div>
                                <label for="register_email" class="block text-sm font-medium text-gray-300 uppercase">Email</label>
                                <input type="email" name="register_email" id="register_email" required 
                                       class="mt-1 block w-full px-3 py-2 bg-gray-800 border border-gray-600 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-white focus:border-transparent">
                            </div>
                            
                            <div>
                                <label for="register_password" class="block text-sm font-medium text-gray-300 uppercase">Mot de passe</label>
                                <input type="password" name="register_password" id="register_password" required 
                                       class="mt-1 block w-full px-3 py-2 bg-gray-800 border border-gray-600 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-white focus:border-transparent">
                            </div>
                            
                            <div>
                                <label for="register_password_confirmation" class="block text-sm font-medium text-gray-300 uppercase">Confirmer le mot de passe</label>
                                <input type="password" name="register_password_confirmation" id="register_password_confirmation" required 
                                       class="mt-1 block w-full px-3 py-2 bg-gray-800 border border-gray-600 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-white focus:border-transparent">
                            </div>
                            
                            <div class="flex items-center">
                                <input type="checkbox" name="register_newsletter" id="register_newsletter" value="1"
                                       class="h-4 w-4 text-white focus:ring-white border-gray-600 bg-gray-800">
                                <label for="register_newsletter" class="ml-2 block text-sm text-gray-300">
                                    Je souhaite recevoir la newsletter d'Astrolab
                                </label>
                            </div>
                            
                            <button type="submit" 
                                    class="w-full py-3 px-4 border border-white text-sm font-medium uppercase text-white bg-transparent hover:bg-white hover:text-black transition-colors duration-200">
                                Créer mon compte et continuer
                            </button>
                        </form>
                    </div>

                    <!-- Se connecter -->
                    <div class="bg-gray-900 p-6 border border-gray-700">
                        <h3 class="text-xl font-semibold mb-4 uppercase">J'ai déjà un compte</h3>
                        <p class="text-gray-400 mb-6">Connectez-vous pour accéder à vos informations sauvegardées.</p>
                        
                        <form action="{{ route('checkout.shipping') }}" method="POST" class="space-y-4">
                            @csrf
                            <input type="hidden" name="checkout_type" value="login">
                            
                            <div>
                                <label for="login_email" class="block text-sm font-medium text-gray-300 uppercase">Email</label>
                                <input type="email" name="login_email" id="login_email" required 
                                       class="mt-1 block w-full px-3 py-2 bg-gray-800 border border-gray-600 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-white focus:border-transparent">
                            </div>
                            
                            <div>
                                <label for="login_password" class="block text-sm font-medium text-gray-300 uppercase">Mot de passe</label>
                                <input type="password" name="login_password" id="login_password" required 
                                       class="mt-1 block w-full px-3 py-2 bg-gray-800 border border-gray-600 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-white focus:border-transparent">
                            </div>
                            
                            <button type="submit" 
                                    class="w-full py-3 px-4 border border-blue-400 text-sm font-medium uppercase text-blue-400 bg-transparent hover:bg-blue-400 hover:text-black transition-colors duration-200">
                                Se connecter et continuer
                            </button>
                        </form>
                    </div>

                    <!-- Continuer en tant qu'invité -->
                    <div class="bg-gray-900 p-6 border border-gray-700">
                        <h3 class="text-xl font-semibold mb-4 uppercase">Continuer en tant qu'invité</h3>
                        <p class="text-gray-400 mb-6">Passez votre commande sans créer de compte.</p>
                        
                        <form action="{{ route('checkout.shipping') }}" method="GET">
                            <input type="hidden" name="checkout_type" value="guest">
                            <button type="submit" 
                                    class="w-full py-3 px-4 border border-gray-600 text-sm font-medium uppercase text-gray-300 bg-transparent hover:border-white hover:text-white transition-colors duration-200">
                                Continuer en tant qu'invité
                            </button>
                        </form>
                    </div>
                @endauth

            </div>
        </div>

        <!-- Navigation -->
        <div class="mt-12 text-center">
            <a href="{{ route('cart.index') }}" 
               class="inline-block py-2 px-4 border border-gray-600 text-sm font-medium uppercase text-gray-300 bg-transparent hover:border-white hover:text-white transition-colors duration-200">
                ← Retour au panier
            </a>
        </div>
    </div>
</div>
@endsection
