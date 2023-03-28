<?php

namespace App\Http\Controllers;

class IndexController extends Controller
{
    public function get() {
        return response()->json(['login']);
    }
    public function post() {
        return response()->json(['login']);
    }
}
