<header>
    <header style="height: 52px;">
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
    </header>
</header>
