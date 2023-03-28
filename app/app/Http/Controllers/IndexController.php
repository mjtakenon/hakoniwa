<?php

namespace App\Http\Controllers;

class IndexController extends Controller
{
    public function get() {
        return response()->json(['index']);//view('index');
    }
}
