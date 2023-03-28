<?php

namespace App\Http\Controllers;

class IndexController extends Controller
{
    public function post() {
        return response()->json(['logout']);
    }
}
