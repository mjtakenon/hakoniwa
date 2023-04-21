<?php

namespace App\Http\Controllers\Test;

use App\Http\Controllers\Controller;
use App\Models\Island;
use App\Models\Turn;

class DetailController extends Controller
{
    public function get1() {
        return response()->json(['test' => 1]);
    }
    public function get2() {
        return response()->json(['test' => 2]);
    }
    public function get3() {
        return redirect(route('home'));
    }
}
