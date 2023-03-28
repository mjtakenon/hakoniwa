<?php

namespace App\Http\Controllers;

class IndexController extends Controller
{
    public function get() {
        return response()->json(['register']);
    }
    public function post() {
        return response()->json(['post register']);
    }
}
