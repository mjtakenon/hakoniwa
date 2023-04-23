<?php

namespace App\Http\Controllers;

use App\Models\Island;
use App\Models\Turn;

class IndexController extends Controller
{
    public function get()
    {
        $turn = Turn::latest()->firstOrFail();

        $islands = Island::where('turn_id', $turn->id)
            ->whereNull('deleted_at')
            ->join('island_statuses','islands.id','=','island_statuses.island_id')
            ->orderBy('island_statuses.development_points', 'desc')
            ->get();

        return view('pages.index', [
            'islands' => $islands,
            'turn' => $turn,
        ]);
    }
}
