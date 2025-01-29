@extends('layouts.app')

@section('content')
    <a class="btn bg-blue-500 text-white" href="/">home</a>
    <form action="{{ route('products.store') }}" method="post">
        @csrf
        <div>
            <label for="name">name</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}">
            @error('name')
                <p>{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="description">description</label>
            <textarea name="description" id="description">{{old('description')}}</textarea>
            @error('description')
                <p>{{ $message }}</p>                
            @enderror
        </div>
        <div>
            <label for="price">price</label>
            <input type="number" name="price" id="price" value="{{ old('price') }}">
            @error('price')
                <p>{{ $message }}</p>                
            @enderror
        </div>
        <div>
            <label for="quantity">quantity</label>
            <input type="number" name="quantity" id="quantity" value="{{ old('quantity') }}">
            @error('quantity')
                <p>{{ $message }}</p>                
            @enderror
        </div>
        <button type="submit">Submit</button>
    </form>

@endsection