<?php

namespace App\Http\Controllers\Web\Islands;

use App\Http\Controllers\Controller;
use App\Models\Island;
use App\Models\IslandLog;
use App\Models\IslandStatus;
use App\Models\Turn;
use Illuminate\Support\Collection;

class PlansController extends Controller
{
    // TODO Consider to reduce count of recent turns log after making log detail page.
    const DEFAULT_SHOW_LOG_TURNS = 20;

    public function get(int $islandId)
    {
        $island = Island::find($islandId);

        if (is_null($island) || !is_null($island->deleted_at)) {
            abort(404);
        }

        if (!\HakoniwaService::isIslandRegistered() || \Auth::user()->island->id !== $island->id) {
            abort(403);
        }

        $turn = Turn::latest()->firstOrFail();
        $getLogRecentTurns = self::DEFAULT_SHOW_LOG_TURNS;

        $islandPlans = $island->islandPlans->where('turn_id', $turn->id)->firstOrFail();
        $islandStatus = $island->islandStatuses->where('turn_id', $turn->id)->firstOrFail();
        $islandTerrain = $island->islandTerrains->where('turn_id', $turn->id)->firstOrFail();
        $islandLogs = $island->islandLogs()
            ->whereIn('turn_id', Turn::where('turn', '>=', $turn->turn - $getLogRecentTurns)->get('id'))
            ->with(['turn'])
            ->orderByDesc('id')
            ->get()
            ->groupBy('turn.turn')
            ->map(function ($groupedLog, $turn) use ($island) {
                /** @var Collection $groupedLog */
                return [
                    'data' => $groupedLog->map(function ($log) {
                        /** @var IslandLog $log */
                        return $log->log;
                    }),
                    'turn' => $turn,
                ];
            });

        $summary = $island->islandStatuses()
            ->whereIn('turn_id', Turn::where('turn', '>=', $turn->turn - ($getLogRecentTurns + 1))->get('id'))
            ->with(['turn'])
            ->orderByDesc('id')
            ->get();

        // TODO: 島の数が多くなってくるとマズいのでいずれ考える
        $targetIslands = Island::get();

        return view('pages.islands.plans', [
            'hakoniwa' => [
                'width' => \HakoniwaService::getMaxWidth(),
                'height' => \HakoniwaService::getMaxHeight(),
            ],
            'island' => [
                'id' => $island->id,
                'name' => $island->name,
                'owner_name' => $island->owner_name,
                'status' => [
                    'development_points' => $islandStatus->development_points,
                    'funds' => $islandStatus->funds,
                    'foods' => $islandStatus->foods,
                    'resources' => $islandStatus->resources,
                    'population' => $islandStatus->population,
                    'funds_production_capacity' => $islandStatus->funds_production_capacity,
                    'foods_production_capacity' => $islandStatus->foods_production_capacity,
                    'resources_production_capacity' => $islandStatus->resources_production_capacity,
                    'environment' => $islandStatus->environment,
                    'area' => $islandStatus->area,
                    'abandoned_turn' => $islandStatus->abandoned_turn,
                ],
                'terrains' => $islandTerrain->toEntity()->toArray(true, true),
                'plans' => $islandPlans->toEntity()->toArray(true),
                'logs' => array_values($islandLogs->toArray()),
                'summary' => $summary->map(function ($status, $index) use ($summary) {
                    if ($summary->count() - 1 > $index) {
                        /** @var IslandStatus | Turn $prevStatus */
                        $prevStatus = $summary->get($index+1);
                        /** @var IslandStatus | Turn $status */
                        $status = $summary->get($index);
                        return [
                            'development_points' => $status->development_points - $prevStatus->development_points,
                            'funds' => $status->funds - $prevStatus->funds,
                            'foods' => $status->foods - $prevStatus->foods,
                            'resources' => $status->resources - $prevStatus->resources,
                            'population' => $status->population - $prevStatus->population,
                            'turn' => $status->turn->turn,
                        ];
                    } else {
                        return null;
                    }
                })->filter(function ($status) { return !is_null($status); }),
            ],
            'executablePlans' => \PlanService::getExecutablePlans($islandStatus->development_points),
            'targetIslands' => $targetIslands->map(function ($targetIsland) {
                return [
                    'id' => $targetIsland->id,
                    'name' => $targetIsland->name,
                    'owner_name' => $targetIsland->owner_name,
                ];
            }),
            'turn' => [
                'turn' => $turn->turn,
                'next_time' => $turn->next_turn_scheduled_at->format('Y-m-d H:i:s')
            ]
        ]);
    }
}
