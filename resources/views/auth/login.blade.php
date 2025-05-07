@extends('layouts.app')

@section('content')

    <div style="display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0;">
        <div style="max-width: 400px; width: 100%; padding: 20px; box-sizing: border-box;">
            <h2 style="font-size: 24px; font-weight: bold; margin-bottom: 16px; text-align: center;">Connexion</h2>

            <form method="POST" action="{{ route('login') }}" style="display: flex; flex-direction: column; gap: 16px;">
                @csrf

                <div>
                    <label for="email" style="display: block; font-size: 14px; font-weight: 500; color: #4a4a4a;">Email</label>
                    <input type="email" name="email" id="email" required style="margin-top: 4px; display: block; width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);">
                </div>

                <div>
                    <label for="password" style="display: block; font-size: 14px; font-weight: 500; color: #4a4a4a;">Mot de passe</label>
                    <input type="password" name="password" id="password" required style="margin-top: 4px; display: block; width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);">
                </div>

                <div style="">
                    <input type="checkbox" name="remember" id="remember" style="margin-top: 4px;">
                    <label for="remember" style="font-size: 14px; color: #4a4a4a;">Se souvenir de moi</label>
                </div>

                <button type="submit">
                    {{ __('Connexion') }}
                </button>

                <a href="{{ route('password.request') }}" style="font-size: 14px; color: #4f46e5; text-decoration: none; text-align: center; margin-top: 8px;">Mot de passe oublié ?</a>

                @if ($errors->any())
                    <div style="margin-top: 16px; background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 8px; border-radius: 4px;" role="alert">
                        <ul style="list-style-type: disc; padding-left: 20px; margin: 0;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </form>

            {{-- <p style="margin-top: 16px; font-size: 14px; text-align: center;">Pas encore de compte ? <a href="{{ route('register') }}" style="color: #4f46e5; text-decoration: none;">Inscris-toi ici</a></p> --}}
        </div>
    </div>
@endsection
