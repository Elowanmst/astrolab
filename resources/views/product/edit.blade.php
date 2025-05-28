<!-- filepath: /Users/macbook/Documents/GitHub/astrolab/resources/views/product/edit.blade.php -->
@extends('layouts.master')

@section('styles')
@vite(['resources/css/admin/dashboard.css'])
@endsection

@section('content')

<div class="main-content">
    <h1>Edit Product</h1>
    <a class="back-btn" href="{{ route('products.index') }}">{{ __('back') }}</a>
    
    <form action="{{ route('products.update', $product->id) }}" method="post" class="admin-form" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="form-group">
            <label for="name" class="form-label">Name</label>
            <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}" class="form-input" required>
        </div>
        
        <div class="form-group">
            <label for="description" class="form-label">Description</label>
            <textarea name="description" id="description" class="form-input">{{ old('description', $product->description) }}</textarea>
        </div>
        
        <div class="form-group">
            <label for="material" class="form-label">Material</label>
            <input type="text" name="material" id="material" value="{{ old('material', $product->material) }}" class="form-input">
        </div>
        
        <div class="form-group">
            <label for="size" class="form-label">Size</label>
            <input type="text" name="size" id="size" value="{{ old('size', $product->size) }}" class="form-input">
        </div>
        
        <div class="form-group">
            <label for="category_id" class="form-label">Category</label>
            <select name="category_id" id="category_id" class="form-select">
                @foreach ($categories as $category)
                <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
                @endforeach
            </select>
        </div>   

        <div class="form-group">
            <label>Couleurs disponibles :</label><br>
            @foreach($colors as $color)
            <label style="margin-right: 10px;">
                <input type="checkbox" name="colors[]" value="{{ $color->id }}" 
                    {{ in_array($color->id, $product->colors->pluck('id')->toArray()) ? 'checked' : '' }}>
                <span style="background: {{ $color->hex }}; width: 20px; height: 20px; display: inline-block; border: 1px solid #ccc;"></span>
                {{ $color->name }}
            </label>
            @endforeach
        </div>
        
        <div class="form-group">
            <label for="price" class="form-label">Price</label>
            <input type="number" name="price" id="price" value="{{ old('price', $product->price) }}" class="form-input">
        </div>
        
        <div class="form-group">
            <label for="images" class="form-label">Add New Images</label>
            <input type="file" name="images[]" id="images" class="form-input" multiple>
        </div>
        
        <div class="form-group">
            <label for="stock" class="form-label">Stock</label>
            <input type="number" name="stock" id="stock" value="{{ old('stock', $product->stock) }}" class="form-input">
        </div>
        
        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>

@endsection