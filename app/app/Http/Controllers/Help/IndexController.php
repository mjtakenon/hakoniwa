<?php

namespace App\Http\Controllers\Help;

use App\Http\Controllers\Controller;

class IndexController extends Controller
{
    public function get() {
        return response()->json(['help']);
    }
}
