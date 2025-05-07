@extends('layouts.admin')

@section('styles')
    @vite(['resources/css/admin/dashboard.css'])
@endsection

@section('content')

    <div class="main-content">


            <h1>{{__('Users')}}</h1>

            <a href="{{ route('users.create') }}" class="btn">{{__('create a new user')}}</a>

            <table class="details">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>{{ __('username') }}</th>
                        <th>Email</th>
                        <th>{{ __('created at') }}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @if ($user->created_at)
                                {{ $user->created_at->format('d/m/Y') }}
                            @else
                                {{ __('N/A') }} <!-- Affiche "N/A" ou un autre texte si la date est manquante -->
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('users.show', $user) }}">{{ __('view') }}</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
    </div>
@endsection