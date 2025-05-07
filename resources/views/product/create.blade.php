@extends('layouts.master')

@section('styles')
    @vite(['resources/css/admin/dashboard.css'])
@endsection

@section('content')

    <div class="main-content">

        <h1>Create a Product</h1>
        <a class="back-btn" href="{{ route('products.index') }}">{{ __('back') }}</a>

        <form action="{{ route('products.store') }}" method="post" class="admin-form" enctype="multipart/form-data">
            @csrf    
            @if ($errors->any())
                <div class="error-messages">
                    <ul class="error-list">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="form-group">
                <label for="name" class="form-label">Name</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" class="form-input" required>
                @error('name')
                    <p class="error-text">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="description" class="form-label">Description</label>
                <textarea name="description" id="description" class="form-input">{{ old('description') }}</textarea>
                @error('description')
                    <p class="error-text">{{ $message }}</p>                
                @enderror
            </div>

            <div class="form-group">
                <label for="color" class="form-label">Color</label>
                <input type="text" name="color" id="color" value="{{ old('color') }}" class="form-input">
                @error('color')
                    <p class="error-text">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="material" class="form-label">Material</label>
                <input type="text" name="material" id="material" value="{{ old('material') }}" class="form-input">
                @error('material')
                    <p class="error-text">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="size" class="form-label">Size</label>
                <input type="text" name="size" id="size" value="{{ old('size') }}" class="form-input">
                @error('size')
                    <p class="error-text">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="category_id" class="form-label">Category</label>
                <select name="category_id" id="category_id" class="form-select">
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                    <p class="error-text">{{ $message }}</p>
                @enderror
            </div>   

            <div class="form-group">
                <label for="price" class="form-label">Price</label>
                <input type="number" name="price" id="price" value="{{ old('price') }}" class="form-input">
                @error('price')
                    <p class="error-text">{{ $message }}</p>                
                @enderror
            </div>

            <div class="form-group">
                <label for="image" class="form-label">Image</label>
                <input type="file" name="image" id="image" class="form-input">
                @error('image')
                    <p class="error-text">{{ $message }}</p>
                @enderror
            </div>


            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
    
@endsection