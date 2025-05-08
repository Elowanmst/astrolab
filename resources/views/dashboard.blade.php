@extends('layouts.master')

@section('styles')
@vite(['resources/css/admin/dashboard.css'])
@endsection

@section('content')


<div class="main-content">
    
    <h1>{{ __('Dashboard') }}</h1>
    
    <div class="stats-container"> 
        
        <div class="card stat">
            <h2 onclick="window.location='{{ route('products.index') }}'" style="cursor: pointer;">{{ __('My products') }}</h2>
            <p>test</p>
        </div>
        
        
        <div class="card stat">
            <h2 onclick="window.location='{{ route('products.index') }}'" style="cursor: pointer;">{{ __('My orders') }}</h2>
            <p>test</p>
        </div>
        
        <div class="card stat">
            <h2 onclick="window.location='{{ route('products.index') }}'" style="cursor: pointer;">{{ __('Users') }}</h2>
            <p>test</p>
        </div>
        
    </div>
    
</div>

@endsection
