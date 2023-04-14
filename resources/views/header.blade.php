<nav class="navbar navbar-expand-lg bg-body-tertiary">
    <div class="container">
        <a href="/" class="navbar-brand">Admin Panel</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown"
            aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavDropdown">
            <ul class="navbar-nav navbar-list">
                <li class="nav-item">
                    <a class="nav-link active" href="/">Home</a>
                </li>
                @auth
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('signout') }}">Sign
                            out</a>
                    </li>
                @endauth
                @guest
                    <li class="nav-item">
                        <a class="nav-link active" href="/admin">Sign in</a>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>
