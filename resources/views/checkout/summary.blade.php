@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-2xl font-bold mb-6">Récapitulatif de votre commande</h1>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Résumé de la commande -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold mb-4">Articles commandés</h2>
                
                @foreach($cart->get() as $item)
                <div class="flex justify-between items-center py-2 border-b">
                    <div class="flex-1">
                        <h3 class="font-medium">{{ $item['name'] }}</h3>
                        @if(isset($item['size']) || isset($item['color']))
                        <p class="text-sm text-gray-600">
                            @if(isset($item['size']))Taille: {{ $item['size'] }}@endif
                            @if(isset($item['size']) && isset($item['color']))<br>@endif
                            @if(isset($item['color']))Couleur: {{ $item['color'] }}@endif
                        </p>
                        @endif
                        <p class="text-sm">Quantité: {{ $item['quantity'] }}</p>
                    </div>
                    <div class="text-right">
                        <span class="font-semibold">{{ number_format($item['price'] * $item['quantity'], 2) }}€</span>
                    </div>
                </div>
                @endforeach
                
                <div class="mt-4 space-y-2">
                    <div class="flex justify-between">
                        <span>Sous-total:</span>
                        <span>{{ number_format($total, 2) }}€</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Livraison:</span>
                        <span>{{ number_format($shippingCost, 2) }}€</span>
                    </div>
                    <div class="flex justify-between text-lg font-bold">
                        <span>Total:</span>
                        <span>{{ number_format($finalTotal, 2) }}€</span>
                    </div>
                </div>
            </div>
            
            <!-- Informations de livraison -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold mb-4">Informations de livraison</h2>
                
                <div class="space-y-2 text-sm">
                    <p><strong>Nom:</strong> {{ $shippingData['shipping_name'] }}</p>
                    <p><strong>Email:</strong> {{ $shippingData['shipping_email'] }}</p>
                    <p><strong>Téléphone:</strong> {{ $shippingData['shipping_phone'] }}</p>
                    
                    @if($shippingData['shipping_method'] === 'home')
                    <p><strong>Adresse:</strong></p>
                    <p>{{ $shippingData['shipping_address'] }}</p>
                    <p>{{ $shippingData['shipping_postal_code'] }} {{ $shippingData['shipping_city'] }}</p>
                    <p><strong>Mode de livraison:</strong> Livraison à domicile</p>
                    @elseif($shippingData['shipping_method'] === 'pickup')
                    <p><strong>Mode de livraison:</strong> Point relais</p>
                    @if(!empty($shippingData['relay_point_name']))
                    <p><strong>Point relais sélectionné:</strong></p>
                    <p>{{ $shippingData['relay_point_name'] }}</p>
                    <p>{{ $shippingData['relay_point_address'] }}</p>
                    <p>{{ $shippingData['relay_point_postal_code'] }} {{ $shippingData['relay_point_city'] }}</p>
                    @endif
                    @endif
                </div>
                
                <div class="mt-6">
                    <form action="{{ route('checkout.process') }}" method="POST">
                        @csrf
                        
                        <div class="mb-4 p-4 bg-blue-50 rounded border border-blue-200">
                            <h3 class="font-semibold text-blue-800 mb-2">Mode de commande</h3>
                            <p class="text-sm text-blue-700">
                                Votre commande sera traitée et préparée pour expédition. 
                                Vous recevrez un email de confirmation avec les détails de suivi.
                            </p>
                        </div>
                        
                        <div class="space-y-4">
                            <button type="submit" 
                                    class="w-full bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition duration-200">
                                Confirmer ma commande
                            </button>
                            
                            <a href="{{ route('checkout.shipping') }}" 
                               class="block w-full text-center bg-gray-200 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-300 transition duration-200">
                                Retour aux informations de livraison
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
