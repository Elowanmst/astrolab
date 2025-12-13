@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-md mx-auto bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-6">
            <h2 class="text-2xl font-bold mb-4">Paiement Stripe</h2>
            <div class="mb-4">
                <h3 class="text-lg font-semibold">Produit Test</h3>
                <p class="text-gray-600">Description du produit test.</p>
                <p class="text-xl font-bold mt-2">20,00 €</p>
            </div>
            
            <form action="{{ route('stripe.process') }}" method="POST">
                @csrf
                <button type="submit" class="w-full bg-blue-600 text-white font-bold py-2 px-4 rounded hover:bg-blue-700 transition duration-150">
                    Payer avec Stripe
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
