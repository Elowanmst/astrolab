@extends('layouts.master')

@section('styles')
    @vite(['resources/css/admin/dashboard.css'])
@endsection

@section('content')
    <div class="main-content">

        <h1>products</h1>

        <a class="btn" href="{{ route('products.create') }}">
            {{ __('add product') }}
        </a>

        <table class="details">
            <thead>
                <tr>
                    <th>{{ __('picture') }}</th>
                    <th>{{ __('name') }}</th>
                    <th>{{ __('price') }}</th>
                    <th>{{ __('category') }}</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $product)
                <tr onclick="window.location='{{ route('products.show', $product) }}'" style="cursor: pointer;">
                    <td>
                        {{-- @if ($product->getFirstMediaUrl('products', 'thumb'))
                            <img src="{{ $product->getFirstMediaUrl('products', 'thumb') }}" alt="{{ $product->brand }} {{ $product->model }}">
                        @else
                            <p>{{ __('No image available') }}</p>
                        @endif --}}
                    </td>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->price }} €</td>
                    <td>
                        @if ($product->category)
                            {{ $product->category->name }}
                        @else
                            {{ __('No category') }}
                        @endif
                    <td>
                        <a href="{{ route('products.edit', $product) }}">{{ __('edit') }}</a>
                        <form action="{{ route('products.destroy', $product) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button class="btn-delete" type="submit">{{ __('delete') }}</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{ $products->links() }}
    </div>

@endsection