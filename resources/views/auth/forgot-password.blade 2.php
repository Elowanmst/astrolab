@extends('layouts.app')

@section('content')

<div style="display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0;">
    <div style="max-width: 400px; width: 100%; padding: 20px; box-sizing: border-box;">
        <h2 style="font-size: 24px; font-weight: bold; margin-bottom: 16px; text-align: center;">{{ __('Forgot Password') }}</h2>

        @if (session('status'))
            <div style="margin-bottom: 16px; font-size: 14px; color: #28a745; text-align: center;">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" style="display: flex; flex-direction: column; gap: 16px;">
            @csrf

            <div>
                <label for="email" style="display: block; font-size: 14px; font-weight: 500; color: #4a4a4a;">{{ __('E-Mail Address') }}</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                    style="margin-top: 4px; display: block; width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);">
                @error('email')
                    <p style="margin-top: 4px; font-size: 14px; color: #e3342f;">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" style="padding: 10px; background-color: #4f46e5; color: white; border: none; border-radius: 4px; cursor: pointer; text-align: center;">
                {{ __('Send Password Reset Link') }}
            </button>
        </form>

        <a href="{{ route('login') }}" style="display: block; margin-top: 16px; text-align: center; font-size: 14px; color: #4f46e5; text-decoration: none;">
            {{ __('Back to Login') }}
        </a>
    </div>
</div>

@endsection
