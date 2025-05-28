@extends('layouts.app')

@section('content')
    <h1>Mon Panier</h1>

    @if(count($cartItems) > 0)
        <table>
            <thead>
                <tr>
                    <th>Produit</th>
                    <th>Prix</th>
                    <th>Quantité</th>
                    <th>Total</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($cartItems as $item)
                    <tr>
                        <td>{{ $item['name'] }}</td>
                        <td>{{ number_format($item['price'], 2) }} €</td>
                        <td>{{ $item['quantity'] }}</td>
                        <td>{{ number_format($item['price'] * $item['quantity'], 2) }} €</td>
                        <td>
                            <form action="{{ route('cart.remove', $item['product_id']) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <p><strong>Total :</strong> {{ number_format($total, 2) }} €</p>
    @else
        <p>Votre panier est vide.</p>
    @endif
@endsection