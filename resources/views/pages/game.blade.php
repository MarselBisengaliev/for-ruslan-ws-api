@extends('layout')

@section('content')
    <main class="container mt-5">
        <h1>Manage - {{ $game->slug }}</h1>
        <hr>
        @if ($game->deletedGame)
            <div class="alert alert-warning" role="alert">
                Game already deleted
            </div>
            <a class="btn btn-primary" href="{{ route('game.restore', ['slug' => $game->slug]) }}">Restore Game</a>
        @else
            <a href="{{ route('game.delete', ['slug' => $game->slug]) }}" class="btn btn-warning">Mark as deleted</a>
        @endif
        <section class="mt-5">
            <ul class="list-group">
                @foreach ($game->scoreCount as $score)
                    <li class="list-group-item">
                        <p><b>{{ $score->user->username }}</b> - {{ $score->score }}</p>
                        <p>
                            <a class="btn btn-danger" href="{{ route('score.delete', ['scoreId' => $score->id]) }}">Delete
                                score</a>
                            <a class="btn btn-danger" href="{{ route('score.delete-all-user-scores', ['userId' => $score->user->id, 'slug' => $game->slug]) }}">Deleteall scores of this user</a>
                        </p>
                    </li>
                @endforeach
            </ul>
        </section>
        <a href="{{ route('score.delete-all', ['slug' => $game->slug]) }}" class="btn btn-warning mt-5 mb-5">Reset the highscores for a game</a>
    </main>
@endsection
