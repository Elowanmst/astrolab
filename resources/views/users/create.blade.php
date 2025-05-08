@extends('layouts.admin')

@section('styles')
    @vite(['resources/css/admin/dashboard.css'])
@endsection

@section('content')

<div class="main-content">
    

    <a class="back-btn" href="{{ route('users.index') }}">{{ __('back') }}</a>
    <h1>{{ __('create a new user') }}</h1>
    <form class="admin-form" action="{{ route('users.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="name">{{ __('name') }}</label>
            <input type="text" name="name" id="name" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="email">{{ __('email') }}</label>
            <input type="email" name="email" id="email" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="password">{{ __('password') }}</label>
            <input type="password" name="password" id="password" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">{{ __('create') }}</button>
    </form>
</div>
@endsection