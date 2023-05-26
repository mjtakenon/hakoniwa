<?php

namespace App\Http\Controllers\Web\Auth\Google;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;

class RedirectController extends Controller
{
    public function get() {
        if (\Auth::check()) {
            return redirect(route(RouteServiceProvider::ROUTE_HOME));
        }
        return \Socialite::driver('google')->redirect();
    }
}
