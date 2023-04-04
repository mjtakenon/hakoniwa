<header>
    <vue-header
        csrf-token="{{ @csrf_token() }}"
        is-logged-in="{{ \Auth::check() }}"
        is-island-registered="{{ (bool)\HakoniwaService::isIslandRegistered() }}"
        @if (\Auth::check())
        :user="{{ \Auth::user() }}"
        :owned-island="{{ \HakoniwaService::getOwnedIsland() }}"
        @endif
    ></vue-header>
</header>
