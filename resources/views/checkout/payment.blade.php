@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-black text-white py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold">ASTROLAB</h1>
            <h2 class="mt-6 text-2xl">PAIEMENT</h2>
            <p class="mt-2 text-gray-400">| ÉTAPE 3/3 : FINALISATION |</p>
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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Récapitulatif détaillé -->
            <div class="lg:col-span-2 space-y-8">
                
                <!-- Informations de livraison -->
                <div class="bg-gray-900 p-6 border border-gray-700">
                    <h3 class="text-xl font-semibold mb-4 uppercase">Adresse de livraison</h3>
                    <div class="text-gray-300">
                        <p class="font-medium">{{ $shippingData['shipping_name'] }}</p>
                        <p>{{ $shippingData['shipping_email'] }}</p>
                        <p>{{ $shippingData['shipping_address'] }}</p>
                        <p>{{ $shippingData['shipping_postal_code'] }} {{ $shippingData['shipping_city'] }}</p>
                        <p class="mt-2">
                            <span class="text-white font-medium">Mode de livraison:</span>
                            {{ $shippingData['shipping_method'] === 'home' ? 'Livraison à domicile' : 'Point relais' }}
                            ({{ number_format($shippingCost, 2) }}€)
                        </p>
                    </div>
                </div>

                <!-- Articles commandés -->
                <div class="bg-gray-900 p-6 border border-gray-700">
                    <h3 class="text-xl font-semibold mb-4 uppercase">Articles commandés</h3>
                    <div class="space-y-4">
                        @foreach($cart->get() as $item)
                            <div class="flex items-center space-x-4 pb-4 border-b border-gray-700">
                                @if($item['image'])
                                    <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="w-20 h-20 object-cover">
                                @else
                                    <div class="w-20 h-20 bg-gray-600 flex items-center justify-center">
                                        <span class="text-gray-400 text-sm">NO IMG</span>
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
                                </div>
                                <div class="text-right">
                                    <p class="font-medium">{{ $item['quantity'] }} x {{ $item['price'] }}€</p>
                                    <p class="text-lg font-bold">{{ number_format($item['price'] * $item['quantity'], 2) }}€</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Formulaire de paiement -->
                <form action="{{ route('checkout.process') }}" method="POST" id="payment-form">
                    @csrf
                    
                    <div class="bg-gray-900 p-6 border border-gray-700">
                        <h3 class="text-xl font-semibold mb-4 uppercase">Informations de paiement</h3>
                        
                        <div class="mb-6">
                            <div class="flex items-center p-4 border border-gray-600 bg-gray-800">
                                <input type="radio" 
                                       name="payment_method" 
                                       id="payment_card" 
                                       value="card" 
                                       checked
                                       class="h-4 w-4 text-white focus:ring-white border-gray-600 bg-gray-800">
                                <label for="payment_card" class="ml-3 flex items-center">
                                    <span class="font-medium text-white uppercase">Carte bancaire</span>
                                    <div class="ml-4 flex space-x-2">
                                        <span class="text-xs bg-blue-600 px-2 py-1 rounded">VISA</span>
                                        <span class="text-xs bg-red-600 px-2 py-1 rounded">MC</span>
                                        <span class="text-xs bg-green-600 px-2 py-1 rounded">CB</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label for="card_number" class="block text-sm font-medium text-gray-300 uppercase">Numéro de carte *</label>
                                <input type="text" 
                                       name="card_number" 
                                       id="card_number" 
                                       placeholder="1234 5678 9012 3456"
                                       maxlength="19"
                                       required 
                                       class="mt-1 block w-full px-3 py-2 bg-gray-800 border border-gray-600 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-white focus:border-transparent">
                            </div>

                            <div>
                                <label for="card_expiry" class="block text-sm font-medium text-gray-300 uppercase">Date d'expiration *</label>
                                <input type="text" 
                                       name="card_expiry" 
                                       id="card_expiry" 
                                       placeholder="MM/AA"
                                       maxlength="5"
                                       required 
                                       class="mt-1 block w-full px-3 py-2 bg-gray-800 border border-gray-600 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-white focus:border-transparent">
                            </div>

                            <div>
                                <label for="card_cvv" class="block text-sm font-medium text-gray-300 uppercase">Code CVV *</label>
                                <input type="text" 
                                       name="card_cvv" 
                                       id="card_cvv" 
                                       placeholder="123"
                                       maxlength="4"
                                       required 
                                       class="mt-1 block w-full px-3 py-2 bg-gray-800 border border-gray-600 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-white focus:border-transparent">
                            </div>

                            <div class="md:col-span-2">
                                <label for="card_name" class="block text-sm font-medium text-gray-300 uppercase">Nom sur la carte *</label>
                                <input type="text" 
                                       name="card_name" 
                                       id="card_name" 
                                       value="{{ $shippingData['shipping_name'] }}"
                                       required 
                                       class="mt-1 block w-full px-3 py-2 bg-gray-800 border border-gray-600 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-white focus:border-transparent">
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Total et validation -->
            <div class="lg:col-span-1 bg-gray-900 p-6 border border-gray-700">
                <h3 class="text-xl font-semibold mb-6 uppercase">Récapitulatif</h3>
                
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span>Sous-total HT:</span>
                        <span>{{ number_format($totalHT, 2) }}€</span>
                    </div>
                    <div class="flex justify-between">
                        <span>TVA (20%):</span>
                        <span>{{ number_format($tva, 2) }}€</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Sous-total TTC:</span>
                        <span>{{ number_format($totalTTC, 2) }}€</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Livraison:</span>
                        <span>{{ number_format($shippingCost, 2) }}€</span>
                    </div>
                    <div class="border-t border-gray-600 pt-3 mt-3">
                        <div class="flex justify-between font-bold text-xl">
                            <span>TOTAL À PAYER:</span>
                            <span>{{ number_format($finalTotal, 2) }}€</span>
                        </div>
                    </div>
                </div>

                <div class="mt-8">
                    <div class="flex items-center mb-4">
                        <input type="checkbox" 
                               id="terms" 
                               required
                               class="h-4 w-4 text-white focus:ring-white border-gray-600 bg-gray-800">
                        <label for="terms" class="ml-2 text-sm text-gray-300">
                            J'accepte les 
                            <a href="#" class="text-white underline">conditions générales de vente</a>
                        </label>
                    </div>

                    <button type="submit" 
                            form="payment-form"
                            id="pay-button"
                            class="w-full py-4 px-4 border border-green-400 text-lg font-medium uppercase text-green-400 bg-transparent hover:bg-green-400 hover:text-black transition-colors duration-200">
                        🔒 Payer {{ number_format($finalTotal, 2) }}€
                    </button>
                </div>

                <div class="mt-4 text-center">
                    <p class="text-xs text-gray-400">
                        🔒 Paiement sécurisé SSL
                    </p>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <div class="mt-12 text-center">
            <a href="{{ route('checkout.shipping') }}" 
               class="inline-block py-2 px-4 border border-gray-600 text-sm font-medium uppercase text-gray-300 bg-transparent hover:border-white hover:text-white transition-colors duration-200">
                ← Retour aux informations de livraison
            </a>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Formatage du numéro de carte
    const cardNumber = document.getElementById('card_number');
    cardNumber.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\s/g, '').replace(/[^0-9]/gi, '');
        let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
        e.target.value = formattedValue;
    });

    // Formatage de la date d'expiration
    const cardExpiry = document.getElementById('card_expiry');
    cardExpiry.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length >= 2) {
            value = value.substring(0, 2) + '/' + value.substring(2, 4);
        }
        e.target.value = value;
    });

    // Validation des champs CVV (seulement des chiffres)
    const cardCvv = document.getElementById('card_cvv');
    cardCvv.addEventListener('input', function(e) {
        e.target.value = e.target.value.replace(/[^0-9]/gi, '');
    });

    // Validation du formulaire
    const form = document.getElementById('payment-form');
    const payButton = document.getElementById('pay-button');
    
    form.addEventListener('submit', function(e) {
        payButton.disabled = true;
        payButton.textContent = 'Traitement en cours...';
        payButton.classList.add('opacity-50');
    });
});
</script>
@endsection
