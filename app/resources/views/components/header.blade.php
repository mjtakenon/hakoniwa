<header>
    <vue-header
        csrf-token="{{ @csrf_token() }}"
        is-logged-in="{{ \Auth::check() }}"
        is-island-registered="{{ \HakoniwaService::isIslandRegistered() }}"
        @if (\Auth::check())
            :user="{{ \Auth::user() }}"
        @endif
        @if (\Auth::check() && \HakoniwaService::isIslandRegistered())
            :owned-island="{{ \HakoniwaService::getOwnedIsland() }}"
        @endif
    ></vue-header>
    @if((env('APP_ENV') == 'local' || env('APP_ENV') == 'development') && file_exists(public_path('hot')))
        <debug-tools></debug-tools>
    @endif
</header>
