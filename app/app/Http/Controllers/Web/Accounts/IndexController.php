<?php

namespace App\Http\Controllers\Web\Accounts;

use App\Http\Controllers\Controller;

class IndexController extends Controller
{
    public function get() {
        return response()->json(['accounts/']);
    }
    public function post() {
        return response()->json(['accounts/']);
    }
}