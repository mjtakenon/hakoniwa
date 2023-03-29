<?php

namespace App\Http\Controllers;
use App\Models\User;

class IndexController extends Controller
{
    public function get() {
        if (\Auth::check()) {
            /** @var User $user */
            $user = \Auth::guard('sanctum')->user();
        }
        return view('pages.index');
    }
}
