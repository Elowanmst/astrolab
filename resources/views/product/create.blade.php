@extends('layouts.app')

@section('content')
    <a class="btn bg-blue-500 text-white" href="/">home</a>
    


    <form action="{{ route('products.store') }}" method="post">
        @csrf    
        @if ($errors->any())
            <div>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
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
            <label for="stock">quantity</label>
            <input type="number" name="stock" id="stock" value="{{ old('stock') }}">
            @error('stock')
                <p>{{ $message }}</p>                
            @enderror
        </div>
        <div>
            <label for="category_id">Category</label>
            <select name="category_id" id="category_id">
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->id }}
                    </option>
                @endforeach
            </select>
            @error('category_id')
                <p>{{ $message }}</p>
            @enderror
        </div>
        <button type="submit">Submit</button>
    </form>

@endsection