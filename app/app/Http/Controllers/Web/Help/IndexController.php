<?php

namespace App\Http\Controllers\Web\Help;

use App\Http\Controllers\Controller;

class IndexController extends Controller
{
    public function get()
    {
        return view('pages.help');
    }
}
