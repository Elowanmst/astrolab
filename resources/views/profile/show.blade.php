@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-black text-white py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-5xl mx-auto">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold tracking-wider">ASTROLAB</h1>
            <h2 class="mt-6 text-3xl font-semibold">MON PROFIL</h2>
            <p class="mt-2 text-gray-400 font-medium tracking-wide">| GÉREZ VOS INFORMATIONS PERSONNELLES |</p>
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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Informations personnelles -->
            <div class="lg:col-span-2 bg-gray-900 p-8 border border-gray-700 rounded-lg shadow-xl">
                <div class="flex items-center mb-8">
                    <i class="fas fa-user-circle text-2xl text-white mr-4"></i>
                    <h3 class="text-2xl font-bold uppercase tracking-wide">Informations personnelles</h3>
                </div>
                
                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-gray-800 p-4 rounded-lg border border-gray-600">
                            <label class="block text-sm font-bold text-gray-300 uppercase tracking-wide mb-2">Nom complet</label>
                            <p class="text-white font-medium text-lg">{{ $user->name }}</p>
                        </div>
                        
                        <div class="bg-gray-800 p-4 rounded-lg border border-gray-600">
                            <label class="block text-sm font-bold text-gray-300 uppercase tracking-wide mb-2">Adresse email</label>
                            <p class="text-white font-medium text-lg">{{ $user->email }}</p>
                        </div>
                    </div>
                    
                    <div class="bg-gray-800 p-4 rounded-lg border border-gray-600">
                        <label class="block text-sm font-bold text-gray-300 uppercase tracking-wide mb-2">Téléphone</label>
                        <p class="text-white font-medium text-lg">{{ $user->phone ?: 'Non renseigné' }}</p>
                    </div>
                    
                    <div class="bg-gray-800 p-4 rounded-lg border border-gray-600">
                        <label class="block text-sm font-bold text-gray-300 uppercase tracking-wide mb-2">Adresse</label>
                        <p class="text-white font-medium text-lg">{{ $user->address ?: 'Non renseignée' }}</p>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-gray-800 p-4 rounded-lg border border-gray-600">
                            <label class="block text-sm font-bold text-gray-300 uppercase tracking-wide mb-2">Ville</label>
                            <p class="text-white font-medium text-lg">{{ $user->city ?: 'Non renseignée' }}</p>
                        </div>
                        <div class="bg-gray-800 p-4 rounded-lg border border-gray-600">
                            <label class="block text-sm font-bold text-gray-300 uppercase tracking-wide mb-2">Code postal</label>
                            <p class="text-white font-medium text-lg">{{ $user->postal_code ?: 'Non renseigné' }}</p>
                        </div>
                    </div>
                    
                    <div class="bg-gray-800 p-4 rounded-lg border border-gray-600">
                        <label class="block text-sm font-bold text-gray-300 uppercase tracking-wide mb-2">Membre depuis</label>
                        <p class="text-white font-medium text-lg">{{ $user->created_at->format('d/m/Y') }}</p>
                    </div>
                </div>
                
                <div class="mt-8">
                    <a href="{{ route('profile.edit') }}" 
                       class="inline-flex items-center py-3 px-6 border-2 border-white text-sm font-bold uppercase text-white bg-transparent hover:bg-white hover:text-black transition-all duration-300 rounded-lg shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                        <i class="fas fa-edit mr-2"></i>
                        Modifier mes informations
                    </a>
                </div>
            </div>

            <!-- Statistiques et actions -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Mes commandes -->
                <div class="bg-gray-900 p-6 border border-gray-700 rounded-lg shadow-xl">
                    <div class="flex items-center mb-6">
                        <i class="fas fa-shopping-bag text-xl text-white mr-3"></i>
                        <h3 class="text-xl font-bold uppercase tracking-wide">Mes commandes</h3>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="flex justify-between items-center py-3 px-4 bg-gray-800 rounded-lg border border-gray-600">
                            <span class="text-gray-300 uppercase font-medium">Total commandes</span>
                            <span class="text-white font-bold text-lg">{{ $user->orders->count() }}</span>
                        </div>
                        
                        <div class="flex justify-between items-center py-3 px-4 bg-gray-800 rounded-lg border border-gray-600">
                            <span class="text-gray-300 uppercase font-medium">Statut</span>
                            <span class="text-green-400 font-bold">
                                <i class="fas fa-check-circle mr-1"></i>
                                Actif
                            </span>
                        </div>
                    </div>
                    
                    @if($user->orders->count() > 0)
                        <div class="mt-6">
                            <h4 class="text-lg font-bold mb-4 uppercase tracking-wide text-gray-300">Dernières commandes</h4>
                            <div class="space-y-3">
                                @foreach($user->orders->sortByDesc('created_at')->take(3) as $order)
                                    <div class="bg-gray-800 p-4 border border-gray-600 rounded-lg shadow-md hover:shadow-lg transition-shadow">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <p class="text-white font-bold text-sm">{{ $order->order_number }}</p>
                                                <p class="text-gray-400 text-xs font-medium mt-1">{{ $order->created_at->format('d/m/Y') }}</p>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-white font-bold">{{ $order->total_amount }}€</p>
                                                <span class="text-xs px-2 py-1 rounded-full font-bold
                                                    @if($order->status === 'delivered') bg-green-600 text-white
                                                    @elseif($order->status === 'shipped') bg-blue-600 text-white
                                                    @elseif($order->status === 'processing') bg-yellow-600 text-black
                                                    @elseif($order->status === 'cancelled') bg-red-600 text-white
                                                    @else bg-gray-600 text-white @endif">
                                                    {{ $order->status_label }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
                
                <!-- Actions rapides -->
                <div class="bg-gray-900 p-6 border border-gray-700 rounded-lg shadow-xl">
                    <div class="flex items-center mb-6">
                        <i class="fas fa-bolt text-xl text-white mr-3"></i>
                        <h3 class="text-xl font-bold uppercase tracking-wide">Actions rapides</h3>
                    </div>
                    
                    <div class="space-y-3">
                        <a href="{{ route('cart.index') }}" 
                           class="flex items-center justify-center w-full py-3 px-4 border-2 border-gray-600 text-sm font-bold uppercase text-gray-300 bg-transparent hover:border-white hover:text-white transition-all duration-300 rounded-lg hover:shadow-lg transform hover:-translate-y-1">
                            <i class="fas fa-shopping-cart mr-2"></i>
                            Voir mon panier
                        </a>
                        
                        <a href="{{ route('home') }}" 
                           class="flex items-center justify-center w-full py-3 px-4 border-2 border-gray-600 text-sm font-bold uppercase text-gray-300 bg-transparent hover:border-white hover:text-white transition-all duration-300 rounded-lg hover:shadow-lg transform hover:-translate-y-1">
                            <i class="fas fa-home mr-2"></i>
                            Continuer mes achats
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions principales -->
        <div class="mt-12 text-center">
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                <a href="{{ route('profile.edit') }}" 
                   class="inline-flex items-center py-4 px-8 border-2 border-white text-sm font-bold uppercase text-white bg-transparent hover:bg-white hover:text-black transition-all duration-300 rounded-lg shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    <i class="fas fa-user-edit mr-2"></i>
                    Modifier mon profil
                </a>
                
                <form method="POST" action="{{ route('logout') }}" class="inline-block">
                    @csrf
                    <button type="submit" 
                            class="inline-flex items-center py-4 px-8 border-2 border-red-600 text-sm font-bold uppercase text-red-600 bg-transparent hover:bg-red-600 hover:text-white transition-all duration-300 rounded-lg shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                        <i class="fas fa-sign-out-alt mr-2"></i>
                        Se déconnecter
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
