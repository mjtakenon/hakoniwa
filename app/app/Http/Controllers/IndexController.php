<?php

namespace App\Http\Controllers;

use App\Models\Island;
use App\Models\IslandLog;
use App\Models\IslandStatus;
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
        ->orderByDesc('id')
        ->get('log');

        return view('pages.index', [
            'islands' => $islands->map(function ($island) {
                /** @var Island | IslandStatus $island */
                return [
                    'id' => $island->id,
                    'name' => $island->name,
                    'owner_name' => $island->owner_name,
                    'development_points' => $island->development_points,
                    'funds' => $island->funds,
                    'foods' => $island->foods,
                    'resources' => $island->resources,
                    'population' => $island->population,
                    'funds_production_number_of_people' => $island->funds_production_number_of_people,
                    'foods_production_number_of_people' => $island->foods_production_number_of_people,
                    'resources_production_number_of_people' => $island->resources_production_number_of_people,
                    'environment' => $island->environment,
                    'area' => $island->area
                ];
            }),
            'turn' => $turn,
            'logs' => $logs,
        ]);
    }
}
