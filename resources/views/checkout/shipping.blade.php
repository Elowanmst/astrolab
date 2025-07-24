@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-black text-white py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold">ASTROLAB</h1>
            <h2 class="mt-6 text-2xl">INFORMATIONS DE LIVRAISON</h2>
            <p class="mt-2 text-gray-400">| ÉTAPE 2/3 : ADRESSE ET MODE DE LIVRAISON |</p>
        </div>
        
        <div class="w-full h-1 bg-white shadow-[0_0_10px_2px_rgba(255,255,255,0.7)] mx-auto mb-12"></div>

        @if ($errors->any())
            <div class="bg-red-600 border border-red-500 text-white px-4 py-3 rounded mb-6">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('checkout.payment') }}" method="POST" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            @csrf
            
            <!-- Formulaire d'adresse -->
            <div class="lg:col-span-2 bg-gray-900 p-6 border border-gray-700">
                <h3 class="text-xl font-semibold mb-6 uppercase">Adresse de livraison</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="shipping_name" class="block text-sm font-medium text-gray-300 uppercase">Nom complet *</label>
                        <input type="text" 
                               name="shipping_name" 
                               id="shipping_name" 
                               value="{{ old('shipping_name', $user?->name) }}"
                               required 
                               class="mt-1 block w-full px-3 py-2 bg-gray-800 border border-gray-600 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-white focus:border-transparent">
                    </div>

                    <div>
                        <label for="shipping_email" class="block text-sm font-medium text-gray-300 uppercase">Email *</label>
                        <input type="email" 
                               name="shipping_email" 
                               id="shipping_email" 
                               value="{{ old('shipping_email', $user?->email) }}"
                               required 
                               class="mt-1 block w-full px-3 py-2 bg-gray-800 border border-gray-600 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-white focus:border-transparent">
                    </div>

                    <div>
                        <label for="shipping_phone" class="block text-sm font-medium text-gray-300 uppercase">Téléphone *</label>
                        <input type="tel" 
                               name="shipping_phone" 
                               id="shipping_phone" 
                               value="{{ old('shipping_phone', $user?->phone) }}"
                               required 
                               class="mt-1 block w-full px-3 py-2 bg-gray-800 border border-gray-600 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-white focus:border-transparent">
                    </div>

                    <div class="md:col-span-2">
                        <label for="shipping_address" class="block text-sm font-medium text-gray-300 uppercase">Adresse *</label>
                        <textarea name="shipping_address" 
                                  id="shipping_address" 
                                  rows="2"
                                  required 
                                  class="mt-1 block w-full px-3 py-2 bg-gray-800 border border-gray-600 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-white focus:border-transparent">{{ old('shipping_address', $user?->address) }}</textarea>
                    </div>

                    <div>
                        <label for="shipping_city" class="block text-sm font-medium text-gray-300 uppercase">Ville *</label>
                        <input type="text" 
                               name="shipping_city" 
                               id="shipping_city" 
                               value="{{ old('shipping_city', $user?->city) }}"
                               required 
                               class="mt-1 block w-full px-3 py-2 bg-gray-800 border border-gray-600 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-white focus:border-transparent">
                    </div>

                    <div>
                        <label for="shipping_postal_code" class="block text-sm font-medium text-gray-300 uppercase">Code postal *</label>
                        <input type="text" 
                               name="shipping_postal_code" 
                               id="shipping_postal_code" 
                               value="{{ old('shipping_postal_code', $user?->postal_code) }}"
                               required 
                               class="mt-1 block w-full px-3 py-2 bg-gray-800 border border-gray-600 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-white focus:border-transparent">
                    </div>
                </div>

                <!-- Mode de livraison -->
                <div class="mt-8">
                    <h4 class="text-lg font-semibold mb-4 uppercase">Mode de livraison</h4>
                    
                    <div class="space-y-4">
                        <div class="flex items-center p-4 border border-gray-600 bg-gray-800">
                            <input type="radio" 
                                   name="shipping_method" 
                                   id="shipping_home" 
                                   value="home" 
                                   checked
                                   class="h-4 w-4 text-white focus:ring-white border-gray-600 bg-gray-800">
                            <label for="shipping_home" class="ml-3 flex-1">
                                <div class="flex justify-between">
                                    <div>
                                        <span class="font-medium text-white uppercase">Livraison à domicile</span>
                                        <p class="text-sm text-gray-400">Livraison en 3-5 jours ouvrés</p>
                                    </div>
                                    <span class="font-medium text-white">4.99€</span>
                                </div>
                            </label>
                        </div>

                        <div class="flex items-center p-4 border border-gray-600 bg-gray-800">
                            <input type="radio" 
                                   name="shipping_method" 
                                   id="shipping_pickup" 
                                   value="pickup"
                                   class="h-4 w-4 text-white focus:ring-white border-gray-600 bg-gray-800">
                            <label for="shipping_pickup" class="ml-3 flex-1">
                                <div class="flex justify-between">
                                    <div>
                                        <span class="font-medium text-white uppercase">Point relais</span>
                                        <p class="text-sm text-gray-400">Livraison en 2-4 jours ouvrés</p>
                                    </div>
                                    <span class="font-medium text-white">2.99€</span>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Résumé de commande -->
            <div class="lg:col-span-1 bg-gray-900 p-6 border border-gray-700">
                <h3 class="text-xl font-semibold mb-6 uppercase">Résumé</h3>
                
                @foreach($cart->get() as $item)
                    <div class="flex items-center space-x-3 mb-4 pb-4 border-b border-gray-700">
                        @if($item['image'])
                            <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="w-12 h-12 object-cover">
                        @else
                            <div class="w-12 h-12 bg-gray-600 flex items-center justify-center">
                                <span class="text-gray-400 text-xs">IMG</span>
                            </div>
                        @endif
                        <div class="flex-1">
                            <h4 class="font-medium text-sm">{{ $item['name'] }}</h4>
                            <p class="text-xs text-gray-400">{{ $item['quantity'] }} x {{ $item['price'] }}€</p>
                        </div>
                    </div>
                @endforeach
                
                <div class="mt-6 space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span>Sous-total HT:</span>
                        <span>{{ number_format($cart->getTotalHT(), 2) }}€</span>
                    </div>
                    <div class="flex justify-between">
                        <span>TVA (20%):</span>
                        <span>{{ number_format($cart->getTVA(), 2) }}€</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Sous-total TTC:</span>
                        <span>{{ number_format($cart->getTotalTTC(), 2) }}€</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Livraison:</span>
                        <span id="shipping-cost">4.99€</span>
                    </div>
                    <div class="border-t border-gray-600 pt-2 mt-2">
                        <div class="flex justify-between font-bold text-lg">
                            <span>Total:</span>
                            <span id="final-total">{{ number_format($cart->getFinalTotal('home'), 2) }}€</span>
                        </div>
                    </div>
                </div>

                <button type="submit" 
                        class="w-full mt-6 py-3 px-4 border border-white text-sm font-medium uppercase text-white bg-transparent hover:bg-white hover:text-black transition-colors duration-200">
                    Continuer vers le paiement
                </button>
            </div>
        </form>

        <!-- Navigation -->
        <div class="mt-12 text-center">
            <a href="{{ route('checkout.index') }}" 
               class="inline-block py-2 px-4 border border-gray-600 text-sm font-medium uppercase text-gray-300 bg-transparent hover:border-white hover:text-white transition-colors duration-200">
                ← Retour
            </a>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const homeShipping = document.getElementById('shipping_home');
    const pickupShipping = document.getElementById('shipping_pickup');
    const shippingCost = document.getElementById('shipping-cost');
    const finalTotal = document.getElementById('final-total');
    
    const baseTotalTTC = {{ $cart->getTotalTTC() }};
    
    function updateTotal() {
        const shipping = homeShipping.checked ? 4.99 : 2.99;
        const total = baseTotalTTC + shipping;
        
        shippingCost.textContent = shipping.toFixed(2) + '€';
        finalTotal.textContent = total.toFixed(2) + '€';
    }
    
    homeShipping.addEventListener('change', updateTotal);
    pickupShipping.addEventListener('change', updateTotal);
});
</script>
@endsection
