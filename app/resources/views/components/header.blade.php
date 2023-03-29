<header>
    <nav class="navbar" role="navigation" aria-label="main navigation">
        <div class="navbar-brand">
          <a class="navbar-item" href="{{config('app.url')}}">
             {{-- <img src="https://bulma.io/images/bulma-logo.png" width="112" height="28"> --}}
             🏝️
          </a>
        </div>

        <div id="navbarBasicExample" class="navbar-menu">
          <div class="navbar-start">
            <a class="navbar-item">
              Home
            </a>
          </div>

          <div class="navbar-end">
            <a href="{{config('app.url').'/auth/google/redirect'}}">
                <img src="{{config('app.url').'/img/btn_google_signin_light_normal_web.png'}}">
            </a>
          </div>
        </div>
      </nav>
</header>
