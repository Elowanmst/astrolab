@extends('layouts.app')

@section('content')

    <div>
        <div>
            <h2>Créer un utilisateur</h2>

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div>
                    <label for="name">Nom</label>
                    <input type="text" name="name" id="name" required>
                </div>

                <div>
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" required>
                </div>

                <div>
                    <label for="password">Mot de passe</label>
                    <input type="password" name="password" id="password" required>
                </div>

                <div>
                    <label for="password_confirmation">Confirmer le mot de passe</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required>
                </div>

                <button type="submit">Register</button>

                @if ($errors->any())
                    <div>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </form>


            {{-- <p>Déjà un compte ? <a href="{{ route('login') }}">Se connecter ici</a></p> --}}
        </div>
    </div>
@endsection