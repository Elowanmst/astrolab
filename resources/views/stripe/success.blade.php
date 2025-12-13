@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-md mx-auto bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-md" role="alert">
        <p class="font-bold text-xl mb-2">Paiement réussi !</p>
        <p class="mb-2">Merci pour votre achat.</p>
        
        @if(isset($order))
            <div class="mt-4 p-4 bg-white rounded shadow-sm">
                <p class="font-semibold">Commande n° {{ $order->order_number }}</p>
                <p class="text-sm text-gray-600">Un email de confirmation a été envoyé à {{ $order->shipping_email }}.</p>
            </div>
        @endif

        <div class="mt-6">
            <a href="/" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition duration-150">Retour à l'accueil</a>
        </div>
    </div>
</div>
@endsection
