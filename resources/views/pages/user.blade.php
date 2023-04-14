@extends('layout')

@section('content')
    <main class="container mt-5">
        <h1>Manage - {{ $user->username }}</h1>
        <hr>
        @if ($user->blocked)
            <div class="alert alert-warning" role="alert">
                User already banned
            </div>
            <a class="btn btn-primary" href="{{ route('user.unblock', ['username' => $user->username]) }}">Unblock User</a>
        @else
            <form action="{{ route('user.block', ['username' => $user->username]) }}" method="post">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Reason</label>
                    <select class="form-select" name="reason">
                        <option value="admin">You have been blocked by an administrator</option>
                        <option value="spam">You have been blocked for spamming</option>
                        <option value="cheating">You have been blocked for cheating</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Block User</button>
            </form>
        @endif
    </main>
@endsection
