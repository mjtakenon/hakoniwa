<?php

namespace App\Http\Controllers\Logout;

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
