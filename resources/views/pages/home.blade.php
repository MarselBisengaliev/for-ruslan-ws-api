@extends('layout')

@section('content')
    <main class="container mt-5">
        <section class="mb-5">
            <h2>List admin users</h2>
            <ul class="list-group">
                @foreach ($admins as $admin)
                    <li class="list-group-item">
                        <p>Username: {{ $admin->username }}</p>
                        <p>Last login timestamp: {{ $admin->last_login_timestamp }}</p>
                        <p>Registered timestamp: {{ $admin->registered_timestamp }}</p>
                    </li>
                @endforeach
            </ul>
        </section>
        <section class="mb-5">
            <h2>Manage plaform users</h2>
            <ul class="list-group">
                @foreach ($users as $user)
                    <li class="list-group-item users-list-item">
                        <a href="{{ route('user', ['username' => $user->username]) }}">{{ $user->username }}</a>
                    </li>
                @endforeach
            </ul>
        </section>
        <section class="mb-5">
            <h2>Manage games</h2>
            <ul class="list-group">
                @foreach ($games as $game)
                    <li class="list-group-item users-list-item">
                        <div class="games-item">
                            <div class="games-item__header">
                                <h3>
                                    <a href="{{ route('game', ['slug' => $game->slug]) }}">{{ $game->title }}</a>
                                    <span>
                                        <a href="#">{{ $game->author->username }}</a>
                                    </span>
                                </h3>
                                <p>
                                    # score submitted: {{ $game->scoreCount->count() }}
                                </p>
                            </div>
                            <div class="games-item__body">
                                <img src={{ asset("storage$game->optional_thumbnail") }} alt="" />

                                <p>{{ $game->description }}</p>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </section>
    </main>
@endsection
