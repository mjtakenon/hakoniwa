<?php

namespace App\Http\Controllers\Islands;

use App\Http\Controllers\Controller;
class PlansController extends Controller
{
    public function get() {
        return response()->json(['islands/{id}/plans']);
    }
    public function post() {
        return response()->json(['islands/{id}/plans']);
    }
}
