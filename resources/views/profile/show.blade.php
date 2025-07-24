@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-black text-white py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold">ASTROLAB</h1>
            <h2 class="mt-6 text-2xl">MON PROFIL</h2>
            <p class="mt-2 text-gray-400">| GÉREZ VOS INFORMATIONS PERSONNELLES |</p>
        </div>
        
        <div class="w-full h-1 bg-white shadow-[0_0_10px_2px_rgba(255,255,255,0.7)] mx-auto mb-12"></div>

        @if(session('success'))
            <div class="bg-green-600 border border-green-500 text-white px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Informations personnelles -->
            <div class="bg-gray-900 p-6 border border-gray-700">
                <h3 class="text-xl font-semibold mb-6 uppercase">Informations personnelles</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 uppercase">Nom complet</label>
                        <p class="mt-1 text-white">{{ $user->name }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-300 uppercase">Adresse email</label>
                        <p class="mt-1 text-white">{{ $user->email }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-300 uppercase">Téléphone</label>
                        <p class="mt-1 text-white">{{ $user->phone ?: 'Non renseigné' }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-300 uppercase">Adresse</label>
                        <p class="mt-1 text-white">{{ $user->address ?: 'Non renseignée' }}</p>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 uppercase">Ville</label>
                            <p class="mt-1 text-white">{{ $user->city ?: 'Non renseignée' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 uppercase">Code postal</label>
                            <p class="mt-1 text-white">{{ $user->postal_code ?: 'Non renseigné' }}</p>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-300 uppercase">Newsletter</label>
                        <p class="mt-1 text-white">
                            @if($user->newsletter)
                                <span class="text-green-400">✓ Abonné</span>
                            @else
                                <span class="text-gray-400">✗ Non abonné</span>
                            @endif
                        </p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-300 uppercase">Membre depuis</label>
                        <p class="mt-1 text-white">{{ $user->created_at->format('d/m/Y') }}</p>
                    </div>
                </div>
                
                <div class="mt-6">
                    <a href="{{ route('profile.edit') }}" 
                       class="inline-block py-2 px-4 border border-white text-sm font-medium uppercase text-white bg-transparent hover:bg-white hover:text-black transition-colors duration-200">
                        Modifier mes informations
                    </a>
                </div>
            </div>

            <!-- Statistiques / Actions -->
            <div class="bg-gray-900 p-6 border border-gray-700">
                <h3 class="text-xl font-semibold mb-6 uppercase">Mes commandes</h3>
                
                <div class="space-y-4">
                    <div class="flex justify-between items-center py-3 border-b border-gray-700">
                        <span class="text-gray-300 uppercase">Commandes passées</span>
                        <span class="text-white font-medium">{{ $user->orders->count() }}</span>
                    </div>
                    
                    <div class="flex justify-between items-center py-3 border-b border-gray-700">
                        <span class="text-gray-300 uppercase">Newsletter</span>
                        <span class="text-white font-medium">
                            @if($user->newsletter)
                                <span class="text-green-400">✓ Abonné</span>
                            @else
                                <span class="text-gray-400">✗ Non abonné</span>
                            @endif
                        </span>
                    </div>
                </div>
                
                @if($user->orders->count() > 0)
                    <div class="mt-6">
                        <h4 class="text-lg font-medium mb-4 uppercase">Dernières commandes</h4>
                        <div class="space-y-3">
                            @foreach($user->orders->latest()->take(3) as $order)
                                <div class="bg-gray-800 p-4 border border-gray-600">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="text-white font-medium">{{ $order->order_number }}</p>
                                            <p class="text-gray-400 text-sm">{{ $order->created_at->format('d/m/Y') }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-white font-medium">{{ $order->total_amount }}€</p>
                                            <span class="text-xs px-2 py-1 rounded 
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
                
                <div class="mt-6 space-y-3">
                    <a href="{{ route('cart.index') }}" 
                       class="block w-full text-center py-2 px-4 border border-gray-600 text-sm font-medium uppercase text-gray-300 bg-transparent hover:border-white hover:text-white transition-colors duration-200">
                        Voir mon panier
                    </a>
                    
                    <a href="{{ route('home') }}" 
                       class="block w-full text-center py-2 px-4 border border-gray-600 text-sm font-medium uppercase text-gray-300 bg-transparent hover:border-white hover:text-white transition-colors duration-200">
                        Continuer mes achats
                    </a>
                </div>
            </div>
        </div>

        <!-- Actions rapides -->
        <div class="mt-12 text-center">
            <div class="space-x-4">
                <a href="{{ route('profile.edit') }}" 
                   class="inline-block py-3 px-6 border border-white text-sm font-medium uppercase text-white bg-transparent hover:bg-white hover:text-black transition-colors duration-200">
                    Modifier mon profil
                </a>
                
                <form method="POST" action="{{ route('logout') }}" class="inline-block">
                    @csrf
                    <button type="submit" 
                            class="py-3 px-6 border border-red-600 text-sm font-medium uppercase text-red-600 bg-transparent hover:bg-red-600 hover:text-white transition-colors duration-200">
                        Se déconnecter
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
