<?php

namespace App\Http\Controllers;

use App\Entity\Log\LogVisibility;
use App\Models\Island;
use App\Models\IslandLog;
use App\Models\IslandStatus;
use App\Models\Turn;
use Illuminate\Support\Collection;

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
            ->get()
            ->groupBy('turn.turn')
            ->map(function ($groupedLog, $turn) {
                /** @var Collection $groupedLog */
                return [
                    'data' => $groupedLog->map(function ($log) {
                        /** @var IslandLog $log */
                        return $log->log;
                    }),
                    'turn' => $turn,
                ];
            });

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
                    'funds_production_capacity' => $island->funds_production_capacity,
                    'foods_production_capacity' => $island->foods_production_capacity,
                    'resources_production_capacity' => $island->resources_production_capacity,
                    'environment' => $island->environment,
                    'area' => $island->area,
                    'abandoned_turn' => $island->abandoned_turn,
                ];
            }),
            'turn' => $turn,
            'logs' => array_values($logs->toArray()),
        ]);
    }
}
