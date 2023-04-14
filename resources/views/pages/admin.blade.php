@extends('layout')


@section('content')
    <main class="container mt-5">
        <h1>Login</h1>
        <form method="POST" action="{{ route('signin') }}">
            @csrf
            <div class="mb-3">
                <input name="username" type="text" class="form-control" placeholder="Username">
            </div>
            <div class="mb-3">
                <input name="password" type="password" class="form-control" placeholder="Password">
            </div>
            <button type="submit" class="btn btn-primary">Sign in</button>
        </form>
    </main>
@endsection
