<?php

namespace App\Http\Controllers;

use App\Entity\Log\LogConst;
use App\Models\Island;
use App\Models\IslandComment;
use App\Models\IslandLog;
use App\Models\IslandStatus;
use App\Models\Turn;
use Hamcrest\Core\Is;
use Illuminate\Support\Collection;

class IndexController extends Controller
{
    const DEFAULT_SHOW_LOG_TURNS = 5;
    public function get()
    {
        $turn = Turn::latest()->firstOrFail();
        $islands = Island::with(['latestComment', 'islandStatuses' =>
            function ($query) use ($turn) {
                $query->where('turn_id', $turn->id)
                    ->orderByDesc('island_statuses.development_points');
            }
        ])->get();

        $logs = IslandLog::whereIn('turn_id',
            Turn::where('turn', '>=', $turn->turn - self::DEFAULT_SHOW_LOG_TURNS)->get('id'))
            ->where('visibility', LogConst::VISIBILITY_GLOBAL)
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
                /** @var Island | IslandStatus | IslandComment $island */

                $status = $island->islandStatuses->first();
                $comment = $island->latestComment()->get()->first();

                return [
                    'id' => $island->id,
                    'name' => $island->name,
                    'owner_name' => $island->owner_name,
                    'comment' => $comment->comment ?? null,
                    'development_points' => $status->development_points,
                    'funds' => $status->funds,
                    'foods' => $status->foods,
                    'resources' => $status->resources,
                    'population' => $status->population,
                    'funds_production_capacity' => $status->funds_production_capacity,
                    'foods_production_capacity' => $status->foods_production_capacity,
                    'resources_production_capacity' => $status->resources_production_capacity,
                    'environment' => $status->environment,
                    'area' => $status->area,
                    'abandoned_turn' => $status->abandoned_turn,
                ];
            }),
            'turn' => $turn,
            'logs' => array_values($logs->toArray()),
        ]);
    }
}
