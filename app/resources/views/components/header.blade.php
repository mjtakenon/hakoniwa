<header>
    <vue-header
        csrf-token="{{ @csrf_token() }}"
        :is-logged-in="@js(\Auth::check())"
        :is-island-registered="@js(\HakoniwaService::isIslandRegistered())"
        @if (\Auth::check())
            :user="{{ \Auth::user() }}"
        @endif
        @if (\Auth::check() && \HakoniwaService::isIslandRegistered())
            :owned-island="{{ \HakoniwaService::getOwnedIsland() }}"
        @endif
    ></vue-header>
    @if(\App::environment('local') && \APP::hasDebugModeEnabled() && file_exists(public_path('hot')))
        <debug-tools
            @if(is_numeric(config('app.hakoniwa.debug.login_using_id')))
                :debug-login-using-id="{{ config('app.hakoniwa.debug.login_using_id') }}"
            @endif
        ></debug-tools>
    @endif
</header>
