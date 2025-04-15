@extends('layouts.app')

@section('content')
    <div class="container bg-slate-50 mx-auto p-4">

    <a class="" href="/products/create">créer un produit</a>

    <a class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full" href="/products/create">
        créer un produit
    </a>

    <h1>products</h1>

    <ul class="list-none p-0 m-0 mt-4 space-y-4 bg-slate-100 rounded-full">
        @foreach ($products as $product)
            <h2 class="text-xl" >
                <a href="{{ route('products.show', $product) }}">{{$product->name}}</a>
            </h2>

        @endforeach
    </ul>

    {{ $products->links() }}
    </div>

@endsection