<?php

namespace App\Http\Controllers;

use App\Models\Island;
use App\Models\IslandLog;
use App\Models\Turn;
use App\Services\Hakoniwa\Log\LogVisibility;

class IndexController extends Controller
{
    const DEFAULT_SHOW_LOG_TURNS = 5;
    public function get()
    {
        $turn = Turn::latest()->firstOrFail();

        $islands = Island::select('*', 'islands.id as id')
            ->where('turn_id', $turn->id)
            ->whereNull('deleted_at')
            ->join('island_statuses','islands.id','=','island_statuses.island_id')
            ->orderBy('island_statuses.development_points', 'desc')
            ->get();

        $logs = IslandLog::whereIn('turn_id',
            Turn::where('turn', '>=', $turn->turn - self::DEFAULT_SHOW_LOG_TURNS)->get('id'))
        ->where('visibility', LogVisibility::VISIBILITY_GLOBAL)
        ->get('log');

        return view('pages.index', [
            'islands' => $islands,
            'turn' => $turn,
            'logs' => $logs,
        ]);
    }
}
