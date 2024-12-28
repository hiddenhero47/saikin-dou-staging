@extends('layouts.basic')

@section('content')
    <div class="flex-center position-ref full-height">
        @if (Route::has('login'))
            <div class="top-right links">
                @auth
                    <a href="{{ url('/home') }}">Home</a>
                @else
                    <a href="{{ route('login') }}">Login</a>

                    @if (Route::has('register'))
                        <a href="{{ route('register') }}">Register</a>
                    @endif
                @endauth
            </div>
        @endif

        <div class="content">
            <div class="title m-b-md">
                {{ucfirst(config('app.name'))}}
            </div>

            <div class="links">
                <a href="https://laravel.com/docs">Laravel Docs</a>
                <a href="https://laracasts.com">Laracasts</a>
                <a href="https://github.com/laravel/laravel">Laravel GitHub</a>
            </div>
        </div>
    </div>
@endsection