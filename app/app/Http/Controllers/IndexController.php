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

        $islandStatuses = IslandStatus::where('turn_id', $turn->id)
            ->orderByDesc('development_points')
            ->with(['island', 'island.islandComments'])
            ->get()
            ->filter(function ($islandStatus) {
                return !is_null($islandStatus->island);
            });

        $logs = IslandLog::whereIn('turn_id', Turn::where('turn', '>=', $turn->turn - self::DEFAULT_SHOW_LOG_TURNS)->get('id'))
            ->where('visibility', LogConst::VISIBILITY_GLOBAL)
            ->orderByDesc('id')
            ->with(['turn'])
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
            'islands' => $islandStatuses->map(function ($status) {
                /** @var IslandStatus $status */

                $island = $status->island;
                $comment = $status->island->islandComments->first();

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
