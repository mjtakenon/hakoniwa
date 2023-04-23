<?php

namespace App\Http\Controllers;

use App\Models\Island;
use App\Models\Turn;

class IndexController extends Controller
{
    public function get()
    {
        $islands = Island::with([
            'orderByDevelopmentPoints' => function ($query) {$query->where('turn_id', Turn::latest()->firstOrFail()->id);},
            'islandStatuses' => function ($query) {$query->where('turn_id', Turn::latest()->firstOrFail()->id);},
        ])
        ->whereNull('deleted_at')
        ->get();

        return view('pages.index', [
            'islands' => $islands,
            'turn' => Turn::latest()->first(),
        ]);
    }
}
