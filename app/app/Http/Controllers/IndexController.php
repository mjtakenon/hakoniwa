<?php

namespace App\Http\Controllers;
use App\Models\User;

class IndexController extends Controller
{
    public function get() {
        return view('pages.index');
    }
}
