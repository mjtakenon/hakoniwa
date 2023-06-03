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
        @if(\App::environment('local') && \APP::hasDebugModeEnabled())
            debug-login-using-id="{{ config('app.hakoniwa.debug.login_using_id') }}"
        @endif
    ></vue-header>
    @if(\App::environment('local') && \APP::hasDebugModeEnabled() && file_exists(public_path('hot')))
        <debug-tools></debug-tools>
    @endif
</header>
