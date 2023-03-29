<?php

namespace App\Http\Controllers\Auth\Google;

use App\Http\Controllers\Controller;

class RedirectController extends Controller
{
    public function get() {
        return \Socialite::driver('google')->redirect();
    }
}
