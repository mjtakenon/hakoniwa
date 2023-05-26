<?php

namespace App\Http\Controllers\Web\Logout;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;

class IndexController extends Controller
{
    public function post() {
        if (\Auth::check()) {
            \Auth::logout();
        }
        return redirect(route(RouteServiceProvider::ROUTE_HOME));
    }
}
