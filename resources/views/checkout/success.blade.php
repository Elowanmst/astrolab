@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-black text-white py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Mode de paiement actuel -->
        <div class="mb-8 p-4 bg-yellow-900 border border-yellow-600 rounded-lg">
            <div class="flex items-center">
                <i class="fas fa-info-circle text-yellow-400 mr-3"></i>
                <div>
                    <div class="font-bold text-yellow-100 uppercase">Mode de paiement actuel</div>
                    <div class="text-yellow-200">
                        @php
                            $processor = config('payment.default_processor');
                            $processorInfo = config('payment.processors.' . $processor);
                        @endphp
                        {{ $processorInfo['name'] ?? 'Non configuré' }} - {{ $processorInfo['description'] ?? '' }}
                        @if($processor === 'simulation')
                            <strong>(Aucun argent réel traité)</strong>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Header de succès -->
        <div class="text-center mb-12">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-green-600 rounded-full mb-6">
                <i class="fas fa-check text-3xl text-white"></i>
            </div>
            <h1 class="text-4xl font-bold mb-4 uppercase tracking-wider">Commande confirmée</h1>
            <p class="text-gray-300 text-lg">
                Merci pour votre commande ! Nous avons bien reçu votre paiement.
            </p>
        </div>

        <!-- Informations de commande -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            
            <!-- Détails de la commande -->
            <div class="bg-gray-900 border border-gray-800 rounded-lg p-6">
                <h2 class="text-xl font-bold mb-4 uppercase tracking-wider border-b border-gray-800 pb-2">
                    <i class="fas fa-receipt mr-2"></i>Détails de la commande
                </h2>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-400 uppercase">Numéro de commande :</span>
                        <span class="font-mono text-white">#{{ $order->order_number }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400 uppercase">Date :</span>
                        <span class="text-white">{{ $order->created_at->format('d/m/Y à H:i') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400 uppercase">Statut :</span>
                        <span class="px-3 py-1 bg-yellow-600 text-yellow-100 rounded text-sm uppercase">
                            {{ $order->getStatusLabel() }}
                        </span>
                    </div>
                    @if($order->payment_status)
                    <div class="flex justify-between">
                        <span class="text-gray-400 uppercase">Paiement :</span>
                        <span class="px-3 py-1 bg-green-600 text-green-100 rounded text-sm uppercase">
                            {{ $order->payment_status }}
                        </span>
                    </div>
                    @endif
                    @if($order->transaction_id)
                    <div class="flex justify-between">
                        <span class="text-gray-400 uppercase">Transaction :</span>
                        <span class="font-mono text-white text-sm">{{ $order->transaction_id }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between border-t border-gray-800 pt-3">
                        <span class="text-gray-400 uppercase font-medium">Total :</span>
                        <span class="text-white font-bold text-lg">{{ number_format($order->total_amount, 2) }} €</span>
                    </div>
                </div>
            </div>

            <!-- Adresse de livraison -->
            <div class="bg-gray-900 border border-gray-800 rounded-lg p-6">
                <h2 class="text-xl font-bold mb-4 uppercase tracking-wider border-b border-gray-800 pb-2">
                    <i class="fas fa-truck mr-2"></i>Adresse de livraison
                </h2>
                <div class="text-white space-y-1">
                    <div class="font-medium">{{ $order->shipping_name }}</div>
                    <div>{{ $order->shipping_address }}</div>
                    <div>{{ $order->shipping_postal_code }} {{ $order->shipping_city }}</div>
                    <div class="text-gray-400 mt-2">{{ $order->shipping_email }}</div>
                </div>
            </div>
        </div>

        <!-- Articles commandés -->
        <div class="bg-gray-900 border border-gray-800 rounded-lg p-6 mb-8">
            <h2 class="text-xl font-bold mb-6 uppercase tracking-wider border-b border-gray-800 pb-2">
                <i class="fas fa-shopping-bag mr-2"></i>Articles commandés
            </h2>
            <div class="space-y-4">
                @foreach($order->items as $item)
                <div class="flex items-center justify-between border-b border-gray-800 pb-4 last:border-b-0 last:pb-0">
                    <div class="flex-1">
                        <h3 class="font-medium text-white uppercase">{{ $item->product_name }}</h3>
                        <div class="text-sm text-gray-400 mt-1">
                            @if($item->size)
                                <span class="mr-3">Taille: {{ $item->size }}</span>
                            @endif
                            @if($item->color)
                                <span>Couleur: {{ $item->color }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="text-center mx-4">
                        <div class="text-gray-400 text-sm uppercase">Quantité</div>
                        <div class="text-white font-medium">{{ $item->quantity }}</div>
                    </div>
                    <div class="text-right">
                        <div class="text-gray-400 text-sm uppercase">Prix unitaire</div>
                        <div class="text-white font-medium">{{ number_format($item->product_price, 2) }} €</div>
                    </div>
                    <div class="text-right ml-6">
                        <div class="text-gray-400 text-sm uppercase">Total</div>
                        <div class="text-white font-bold">{{ number_format($item->product_price * $item->quantity, 2) }} €</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Étapes suivantes -->
        <div class="bg-gray-900 border border-gray-800 rounded-lg p-6 mb-8">
            <h2 class="text-xl font-bold mb-4 uppercase tracking-wider border-b border-gray-800 pb-2">
                <i class="fas fa-info-circle mr-2"></i>Étapes suivantes
            </h2>
            <div class="space-y-3 text-gray-300">
                <div class="flex items-start">
                    <i class="fas fa-envelope mt-1 mr-3 text-blue-400"></i>
                    <div>
                        <div class="font-medium text-white">Confirmation par email</div>
                        <div class="text-sm">
                            @if(config('payment.default_processor') === 'simulation')
                                Email de confirmation de test envoyé à {{ $order->shipping_email }}
                            @else
                                Un email de confirmation a été envoyé à {{ $order->shipping_email }}
                            @endif
                        </div>
                    </div>
                </div>
                <div class="flex items-start">
                    <i class="fas fa-box mt-1 mr-3 text-yellow-400"></i>
                    <div>
                        <div class="font-medium text-white">Préparation de la commande</div>
                        <div class="text-sm">Votre commande sera préparée dans les 24-48h ouvrées</div>
                    </div>
                </div>
                <div class="flex items-start">
                    <i class="fas fa-shipping-fast mt-1 mr-3 text-green-400"></i>
                    <div>
                        <div class="font-medium text-white">Expédition</div>
                        <div class="text-sm">Vous recevrez un numéro de suivi par email dès l'expédition</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="text-center space-y-4">
            @auth
                <a href="{{ route('profile.orders') }}" 
                   class="inline-flex items-center px-6 py-3 bg-white text-black font-bold uppercase tracking-wider hover:bg-gray-100 transition-colors duration-200 mr-4">
                    <i class="fas fa-list mr-2"></i>Mes commandes
                </a>
            @endauth
            
            <a href="{{ route('home') }}" 
               class="inline-flex items-center px-6 py-3 border-2 border-white text-white font-bold uppercase tracking-wider hover:bg-white hover:text-black transition-colors duration-200">
                <i class="fas fa-home mr-2"></i>Retour à l'accueil
            </a>
        </div>

    </div>
</div>
@endsection
