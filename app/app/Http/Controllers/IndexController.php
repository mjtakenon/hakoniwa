<?php

namespace App\Http\Controllers;

use App\Models\Island;

class IndexController extends Controller
{
    public function get()
    {
        $islands = Island::with(['islandStatuses' => function ($query) {
            $query->where('turn_id', \HakoniwaService::getLatestTurn()->id);
        }])->whereNull('deleted_at')->get();

        return view('pages.index', [
            'islands' => $islands,
        ]);
    }
}
