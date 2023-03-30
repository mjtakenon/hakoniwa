<?php

namespace App\Http\Controllers\Islands;

use App\Http\Controllers\Controller;
class DetailController extends Controller
{
    public function get() {
        return response()->json(['islands/detail']);
    }
}
