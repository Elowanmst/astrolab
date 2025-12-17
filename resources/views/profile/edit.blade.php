@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-black text-white py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-3xl mx-auto">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold tracking-wider">ASTROLAB</h1>
            <h2 class="mt-6 text-3xl font-semibold">MODIFIER MON PROFIL</h2>
            <p class="mt-2 text-gray-400 font-medium tracking-wide">| METTEZ À JOUR VOS INFORMATIONS |</p>
        </div>
        
        <div class="w-full h-1 bg-white shadow-[0_0_10px_2px_rgba(255,255,255,0.7)] mx-auto mb-12"></div>

        @if(session('success'))
            <div class="bg-green-900 border border-green-500 text-green-200 px-6 py-4 rounded-lg mb-8 shadow-lg">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-3 text-green-400"></i>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-900 border border-red-500 text-red-200 px-6 py-4 rounded-lg mb-8 shadow-lg">
                <div class="flex items-start">
                    <i class="fas fa-exclamation-triangle mr-3 text-red-400 mt-1"></i>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li class="font-medium">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 gap-8">
            <!-- Modification des informations personnelles -->
            <div class="bg-[#222222] p-8 border border-gray-700 rounded-lg shadow-xl">
                <div class="flex items-center mb-8">
                    <i class="fas fa-user-edit text-2xl text-white mr-4"></i>
                    <h3 class="text-2xl font-bold uppercase tracking-wide">Informations personnelles</h3>
                </div>
                
                <form method="POST" action="{{ route('profile.update') }}" class="space-y-6">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-bold text-gray-300 uppercase tracking-wide mb-2">
                                Nom complet
                            </label>
                            <input type="text" 
                                   name="name" 
                                   id="name" 
                                   value="{{ old('name', $user->name) }}"
                                   required 
                                   class="block w-full px-4 py-3 bg-gray-800 border-2 border-gray-600 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-white focus:border-white transition-all duration-300 rounded-lg font-medium">
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-bold text-gray-300 uppercase tracking-wide mb-2">
                                Adresse Email
                            </label>
                            <input type="email" 
                                   name="email" 
                                   id="email" 
                                   value="{{ old('email', $user->email) }}"
                                   required 
                                   class="block w-full px-4 py-3 bg-gray-800 border-2 border-gray-600 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-white focus:border-white transition-all duration-300 rounded-lg font-medium">
                        </div>
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-bold text-gray-300 uppercase tracking-wide mb-2">
                            Téléphone
                        </label>
                        <input type="tel" 
                               name="phone" 
                               id="phone" 
                               value="{{ old('phone', $user->phone) }}"
                               class="block w-full px-4 py-3 bg-gray-800 border-2 border-gray-600 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-white focus:border-white transition-all duration-300 rounded-lg font-medium">
                    </div>

                    <div>
                        <label for="address" class="block text-sm font-bold text-gray-300 uppercase tracking-wide mb-2">
                            Adresse
                        </label>
                        <textarea name="address" 
                                  id="address" 
                                  rows="3"
                                  class="block w-full px-4 py-3 bg-[#333333] border-2 border-gray-600 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-white focus:border-white transition-all duration-300 rounded-lg font-medium resize-none">{{ old('address', $user->address) }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="city" class="block text-sm font-bold text-gray-300 uppercase tracking-wide mb-2">
                                Ville
                            </label>
                            <input type="text" 
                                   name="city" 
                                   id="city" 
                                   value="{{ old('city', $user->city) }}"
                                   class="block w-full px-4 py-3 bg-gray-800 border-2 border-gray-600 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-white focus:border-white transition-all duration-300 rounded-lg font-medium">
                        </div>
                        <div>
                            <label for="postal_code" class="block text-sm font-bold text-gray-300 uppercase tracking-wide mb-2">
                                Code postal
                            </label>
                            <input type="text" 
                                   name="postal_code" 
                                   id="postal_code" 
                                   value="{{ old('postal_code', $user->postal_code) }}"
                                   class="block w-full px-4 py-3 bg-gray-800 border-2 border-gray-600 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-white focus:border-white transition-all duration-300 rounded-lg font-medium">
                        </div>
                    </div>

                    <div class="pt-6">
                        <button type="submit" 
                                class="w-full inline-flex items-center justify-center py-4 px-6 border-2 border-white text-sm font-bold uppercase text-white bg-transparent hover:bg-white hover:text-black focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white transition-all duration-300 rounded-lg shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                            <i class="fas fa-save mr-2"></i>
                            Mettre à jour mes informations
                        </button>
                    </div>
                </form>
            </div>

            <!-- Modification du mot de passe -->
            <div class="bg-[#222222] p-8 border border-gray-700 rounded-lg shadow-xl">
                <div class="flex items-center mb-8">
                    <i class="fas fa-key text-2xl text-yellow-500 mr-4"></i>
                    <h3 class="text-2xl font-bold uppercase tracking-wide">Changer le mot de passe</h3>
                </div>
                
                <form method="POST" action="{{ route('profile.password') }}" class="space-y-6">
                    @csrf
                    @method('PUT')
                    
                    <div>
                        <label for="current_password" class="block text-sm font-bold text-gray-300 uppercase tracking-wide mb-2">
                            Mot de passe actuel
                        </label>
                        <input type="password" 
                               name="current_password" 
                               id="current_password" 
                               required 
                               class="block w-full px-4 py-3 bg-gray-800 border-2 border-gray-600 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition-all duration-300 rounded-lg font-medium">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="password" class="block text-sm font-bold text-gray-300 uppercase tracking-wide mb-2">
                                Nouveau mot de passe
                            </label>
                            <input type="password" 
                                   name="password" 
                                   id="password" 
                                   required 
                                   class="block w-full px-4 py-3 bg-gray-800 border-2 border-gray-600 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition-all duration-300 rounded-lg font-medium">
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-bold text-gray-300 uppercase tracking-wide mb-2">
                                Confirmer le nouveau mot de passe
                            </label>
                            <input type="password" 
                                   name="password_confirmation" 
                                   id="password_confirmation" 
                                   required 
                                   class="block w-full px-4 py-3 bg-gray-800 border-2 border-gray-600 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition-all duration-300 rounded-lg font-medium">
                        </div>
                    </div>

                    <div class="pt-6">
                        <button type="submit" 
                                class="w-full inline-flex items-center justify-center py-4 px-6 border-2 border-yellow-500 text-sm font-bold uppercase text-yellow-500 bg-transparent hover:bg-yellow-500 hover:text-black focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition-all duration-300 rounded-lg shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                            <i class="fas fa-lock mr-2"></i>
                            Changer le mot de passe
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Navigation -->
        <div class="mt-12 text-center">
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                <a href="{{ route('profile') }}" 
                   class="inline-flex items-center py-3 px-6 border-2 border-gray-600 text-sm font-bold uppercase text-gray-300 bg-transparent hover:border-white hover:text-white transition-all duration-300 rounded-lg shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Retour au profil
                </a>
                
                <a href="{{ route('home') }}" 
                   class="inline-flex items-center py-3 px-6 border-2 border-gray-600 text-sm font-bold uppercase text-gray-300 bg-transparent hover:border-white hover:text-white transition-all duration-300 rounded-lg shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    <i class="fas fa-home mr-2"></i>
                    Retour à l'accueil
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
