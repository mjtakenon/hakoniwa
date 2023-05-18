<?php

namespace App\Http\Controllers\Web\Islands;

use App\Http\Controllers\Controller;

class BbsController extends Controller
{
    public function get() {
        return response()->json(['islands/{id}/bbs']);
    }
    public function post() {
        return response()->json(['islands/{id}/bbs']);
    }
}
