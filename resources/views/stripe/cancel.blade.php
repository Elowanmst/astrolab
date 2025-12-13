@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-md mx-auto bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded shadow-md" role="alert">
        <p class="font-bold">Paiement annulé</p>
        <p>Vous avez annulé le processus de paiement.</p>
        <div class="mt-4">
            <a href="{{ route('stripe.checkout') }}" class="text-yellow-700 underline hover:text-yellow-900">Réessayer</a>
        </div>
    </div>
</div>
@endsection
