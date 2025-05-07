@extends('layouts.admin')

@section('styles')
@vite(['resources/css/admin/dashboard.css'])
@endsection

@section('content')

<div class="main-content">
    
    <a href="{{ route('users.index') }}" class="back-btn">{{ __('back') }}</a>
    
    <div class="admin-show">
        <h1 class="">{{ $users->name }}</h1>
        <p class="">{{ $users->email }}</p>
        <p class=""><small>{{ __('created at') }} {{ $users->created_at->format('M d, Y') }}</small></p>
        
        <div class="btn-show">
            <a href="{{ route('users.edit', $users) }}" class="btn-edit">{{ __('edit') }}</a>
            <form action="{{ route('users.destroy', $users) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-delete" >{{ __('delete') }}</button>
            </form>
        </div>
    </div>

</div>

@endsection