<?php

namespace App\Http\Controllers\Auth\Google;

class RedirectController extends Controller
{
    public function get() {
        return Socialite::driver('google')->redirect();
    }
}
