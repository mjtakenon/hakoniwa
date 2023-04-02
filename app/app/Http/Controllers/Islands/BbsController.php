<?php

namespace App\Http\Controllers\Islands;

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
