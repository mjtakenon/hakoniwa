<?php

namespace App\Http\Controllers\Web\Auth\Google;

use App\Http\Controllers\Controller;

class RedirectController extends Controller
{
    public function get() {
        if (\Auth::check()) {
            return redirect(route('home'));
        }
        return \Socialite::driver('google')->redirect();
    }
}
