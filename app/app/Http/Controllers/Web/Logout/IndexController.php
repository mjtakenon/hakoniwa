<?php

namespace App\Http\Controllers\Web\Logout;

use App\Http\Controllers\Controller;

class IndexController extends Controller
{
    public function post() {
        if (\Auth::check()) {
            \Auth::logout();
        }
        return redirect(route('home'));
    }
}
