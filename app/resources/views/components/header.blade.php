<header>
    <nav class="navbar" role="navigation" aria-label="main navigation">
        <div class="navbar-brand">
            <a class="navbar-item" href="{{ config('app.url') }}">
                {{-- <img src="https://bulma.io/images/bulma-logo.png" width="112" height="28"> --}}
                🏝️
            </a>

            <a role="button" class="navbar-burger" aria-label="menu" aria-expanded="false" data-target="navbarBasicExample">
{{--                <span aria-hidden="true"></span>--}}
{{--                <span aria-hidden="true"></span>--}}
{{--                <span aria-hidden="true"></span>--}}
            </a>
        </div>

        <div id="navbarBasicExample" class="navbar-menu">
            <div class="navbar-start">
                <a class="navbar-item" href="{{ config('app.url') }}">
                    Home
                </a>
            </div>

            <div class="navbar-end">
                @if(\Auth::check())
                    <div class="navbar-item">
                        {{ \Auth::user()->name }}
                    </div>
                    @if(\HakoniwaService::isIslandRegisterd())
                        <div class="navbar-item">
                            <a class="button is-primary" href="{{ config('app.url').'/islands/' . \HakoniwaService::getOwnedIsland()->id . '/plans' }}"> {{-- fixme:island_id --}}
                                開発画面に行く
                            </a>
                        </div>
                    @else
                        <div class="navbar-item">
                            <a class="button is-primary" href="{{ config('app.url').'/register' }}">
                                島を探しに行く（新規登録）
                            </a>
                        </div>
                    @endif
                    <div class="navbar-item">
                        <form method="POST" name="logout" action="{{ config('app.url').'/logout' }}">
                            @csrf
                            <a class="button" href="javascript:logout.submit()">
                                ログアウト
                            </a>
                        </form>
                    </div>
                @else
                    <a href="{{ config('app.url').'/auth/google/redirect' }}">
                        <img src="{{ config('app.url').'/img/btn_google_signin_light_normal_web.png' }}">
                    </a>
                @endif
            </div>
        </div>
    </nav>
</header>
