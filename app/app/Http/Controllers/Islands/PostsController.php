<?php

namespace App\Http\Controllers\Islands;

use App\Http\Controllers\Controller;
class PostsController extends Controller
{
    public function get() {
        return response()->json(['islands/{id}/posts']);
    }
    public function post() {
        return response()->json(['islands/{id}/posts']);
    }
}
