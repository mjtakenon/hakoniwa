<?php

namespace App\Http\Controllers;

class IndexController extends Controller
{
    public function get() {
        return response()->json(['islands/{id}/plans']);
    }
    public function post() {
        return response()->json(['islands/{id}/plans']);
    }
}
