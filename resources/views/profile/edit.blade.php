@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-black text-white py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl mx-auto">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold">ASTROLAB</h1>
            <h2 class="mt-6 text-2xl">MODIFIER MON PROFIL</h2>
            <p class="mt-2 text-gray-400">| METTEZ À JOUR VOS INFORMATIONS |</p>
        </div>
        
        <div class="w-full h-1 bg-white shadow-[0_0_10px_2px_rgba(255,255,255,0.7)] mx-auto mb-12"></div>

        @if(session('success'))
            <div class="bg-green-600 border border-green-500 text-white px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-600 border border-red-500 text-white px-4 py-3 rounded mb-6">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 gap-8">
            <!-- Modification des informations personnelles -->
            <div class="bg-gray-900 p-6 border border-gray-700">
                <h3 class="text-xl font-semibold mb-6 uppercase">Informations personnelles</h3>
                
                <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
                    @csrf
                    @method('PUT')
                    
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-300 uppercase">
                            Nom complet
                        </label>
                        <input type="text" 
                               name="name" 
                               id="name" 
                               value="{{ old('name', $user->name) }}"
                               required 
                               class="mt-1 block w-full px-3 py-2 bg-gray-800 border border-gray-600 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-white focus:border-transparent">
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-300 uppercase">
                            Adresse Email
                        </label>
                        <input type="email" 
                               name="email" 
                               id="email" 
                               value="{{ old('email', $user->email) }}"
                               required 
                               class="mt-1 block w-full px-3 py-2 bg-gray-800 border border-gray-600 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-white focus:border-transparent">
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-300 uppercase">
                            Téléphone
                        </label>
                        <input type="tel" 
                               name="phone" 
                               id="phone" 
                               value="{{ old('phone', $user->phone) }}"
                               class="mt-1 block w-full px-3 py-2 bg-gray-800 border border-gray-600 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-white focus:border-transparent">
                    </div>

                    <div>
                        <label for="address" class="block text-sm font-medium text-gray-300 uppercase">
                            Adresse
                        </label>
                        <textarea name="address" 
                                  id="address" 
                                  rows="2"
                                  class="mt-1 block w-full px-3 py-2 bg-gray-800 border border-gray-600 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-white focus:border-transparent">{{ old('address', $user->address) }}</textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="city" class="block text-sm font-medium text-gray-300 uppercase">
                                Ville
                            </label>
                            <input type="text" 
                                   name="city" 
                                   id="city" 
                                   value="{{ old('city', $user->city) }}"
                                   class="mt-1 block w-full px-3 py-2 bg-gray-800 border border-gray-600 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-white focus:border-transparent">
                        </div>
                        <div>
                            <label for="postal_code" class="block text-sm font-medium text-gray-300 uppercase">
                                Code postal
                            </label>
                            <input type="text" 
                                   name="postal_code" 
                                   id="postal_code" 
                                   value="{{ old('postal_code', $user->postal_code) }}"
                                   class="mt-1 block w-full px-3 py-2 bg-gray-800 border border-gray-600 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-white focus:border-transparent">
                        </div>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" 
                               name="newsletter" 
                               id="newsletter" 
                               value="1"
                               {{ old('newsletter', $user->newsletter) ? 'checked' : '' }}
                               class="h-4 w-4 text-white focus:ring-white border-gray-600 bg-gray-800">
                        <label for="newsletter" class="ml-2 block text-sm text-gray-300">
                            Je souhaite recevoir la newsletter d'Astrolab
                        </label>
                    </div>

                    <div class="pt-4">
                        <button type="submit" 
                                class="w-full py-3 px-4 border border-white text-sm font-medium uppercase text-white bg-transparent hover:bg-white hover:text-black focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white transition-colors duration-200">
                            Mettre à jour mes informations
                        </button>
                    </div>
                </form>
            </div>

            <!-- Modification du mot de passe -->
            <div class="bg-gray-900 p-6 border border-gray-700">
                <h3 class="text-xl font-semibold mb-6 uppercase">Changer le mot de passe</h3>
                
                <form method="POST" action="{{ route('profile.password') }}" class="space-y-4">
                    @csrf
                    @method('PUT')
                    
                    <div>
                        <label for="current_password" class="block text-sm font-medium text-gray-300 uppercase">
                            Mot de passe actuel
                        </label>
                        <input type="password" 
                               name="current_password" 
                               id="current_password" 
                               required 
                               class="mt-1 block w-full px-3 py-2 bg-gray-800 border border-gray-600 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-white focus:border-transparent">
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-300 uppercase">
                            Nouveau mot de passe
                        </label>
                        <input type="password" 
                               name="password" 
                               id="password" 
                               required 
                               class="mt-1 block w-full px-3 py-2 bg-gray-800 border border-gray-600 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-white focus:border-transparent">
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-300 uppercase">
                            Confirmer le nouveau mot de passe
                        </label>
                        <input type="password" 
                               name="password_confirmation" 
                               id="password_confirmation" 
                               required 
                               class="mt-1 block w-full px-3 py-2 bg-gray-800 border border-gray-600 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-white focus:border-transparent">
                    </div>

                    <div class="pt-4">
                        <button type="submit" 
                                class="w-full py-3 px-4 border border-yellow-500 text-sm font-medium uppercase text-yellow-500 bg-transparent hover:bg-yellow-500 hover:text-black focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition-colors duration-200">
                            Changer le mot de passe
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Navigation -->
        <div class="mt-8 text-center space-x-4">
            <a href="{{ route('profile') }}" 
               class="inline-block py-2 px-4 border border-gray-600 text-sm font-medium uppercase text-gray-300 bg-transparent hover:border-white hover:text-white transition-colors duration-200">
                Retour au profil
            </a>
            
            <a href="{{ route('home') }}" 
               class="inline-block py-2 px-4 border border-gray-600 text-sm font-medium uppercase text-gray-300 bg-transparent hover:border-white hover:text-white transition-colors duration-200">
                Retour à l'accueil
            </a>
        </div>
    </div>
</div>
@endsection
