@extends('layouts.app')

@section('content')
<div class="min-h-screen text-white flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <h1 class="text-center text-4xl font-bold">ASTROLAB</h1>
            <h2 class="mt-6 text-center text-2xl">CONNEXION</h2>
            <p class="mt-2 text-center text-gray-400">| ACCÉDEZ À VOTRE COMPTE |</p>
        </div>
        
        <div class="w-full h-1 bg-white shadow-[0_0_10px_2px_rgba(255,255,255,0.7)] mx-auto"></div>
        
        @if ($errors->any())
            <div class="bg-red-600 border border-red-500 text-white px-4 py-3 rounded">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-6">
            @csrf
            
            <div class="space-y-4">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-300 uppercase">
                        Adresse Email
                    </label>
                    <input type="email" 
                           name="email" 
                           id="email" 
                           value="{{ old('email') }}"
                           required 
                           class="mt-1 block w-full px-3 py-2 bg-gray-800 border border-gray-600 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-white focus:border-transparent">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-300 uppercase">
                        Mot de passe
                    </label>
                    <input type="password" 
                           name="password" 
                           id="password" 
                           required 
                           class="mt-1 block w-full px-3 py-2 bg-gray-800 border border-gray-600 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-white focus:border-transparent">
                </div>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input type="checkbox" 
                           name="remember" 
                           id="remember" 
                           class="h-4 w-4 text-white focus:ring-white border-gray-600 bg-gray-800">
                    <label for="remember" class="ml-2 block text-sm text-gray-300">
                        Se souvenir de moi
                    </label>
                </div>

                <div class="text-sm">
                    <a href="{{ route('password.request') }}" class="text-gray-400 hover:text-white uppercase">
                        Mot de passe oublié ?
                    </a>
                </div>
            </div>

            <div>
                <button type="submit" 
                        class="group relative w-full flex justify-center py-3 px-4 border border-white text-sm font-medium uppercase text-white bg-transparent hover:bg-white hover:text-black focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white transition-colors duration-200">
                    Se connecter
                </button>
            </div>

            <div class="text-center">
                <p class="text-gray-400">
                    Pas encore de compte ? 
                    <a href="{{ route('register') }}" class="text-white hover:text-gray-300 uppercase underline">
                        Créer un compte
                    </a>
                </p>
            </div>
        </form>
    </div>
</div>
@endsection
