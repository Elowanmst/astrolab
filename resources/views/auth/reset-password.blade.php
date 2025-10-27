@extends('layouts.app')

@section('content')
<div class="min-h-screen text-white flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <h1 class="text-center text-4xl font-bold">ASTROLAB</h1>
            <h2 class="mt-6 text-center text-2xl">NOUVEAU MOT DE PASSE</h2>
            <p class="mt-2 text-center text-gray-400">| CRÉEZ UN NOUVEAU MOT DE PASSE |</p>
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

        <form method="POST" action="{{ route('password.update') }}" class="mt-8 space-y-6">
            @csrf
            
            <input type="hidden" name="token" value="{{ $request->route('token') }}">
            
            <div class="space-y-4">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-300 uppercase">
                        Adresse Email
                    </label>
                    <input type="email" 
                           name="email" 
                           id="email" 
                           value="{{ old('email', $request->email) }}"
                           required 
                           autocomplete="email"
                           readonly
                           class="mt-1 block w-full px-3 py-2 bg-gray-700 border border-gray-600 text-gray-300 placeholder-gray-400 focus:outline-none cursor-not-allowed">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-300 uppercase">
                        Nouveau mot de passe
                    </label>
                    <input type="password" 
                           name="password" 
                           id="password" 
                           required 
                           autocomplete="new-password"
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
                           autocomplete="new-password"
                           class="mt-1 block w-full px-3 py-2 bg-gray-800 border border-gray-600 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-white focus:border-transparent">
                </div>
            </div>

            <div>
                <button type="submit" 
                        class="group relative w-full flex justify-center py-3 px-4 border border-white text-sm font-medium uppercase text-white bg-transparent hover:bg-white hover:text-black focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white transition-colors duration-200">
                    Réinitialiser le mot de passe
                </button>
            </div>

            <div class="text-center">
                <p class="text-gray-400">
                    Retour à la 
                    <a href="{{ route('login') }}" class="text-white hover:text-gray-300 uppercase underline">
                        Connexion
                    </a>
                </p>
            </div>
        </form>
    </div>
</div>
@endsection
