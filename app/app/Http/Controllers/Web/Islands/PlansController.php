<?php

namespace App\Http\Controllers\Web\Islands;

use App\Entity\Achievement\Achievements;
use App\Http\Controllers\Controller;
use App\Models\Island;
use App\Models\IslandBbs;
use App\Models\IslandLog;
use App\Models\IslandStatus;
use App\Models\IslandTerrain;
use App\Models\Turn;
use Illuminate\Support\Collection;

class PlansController extends Controller
{
    // TODO Consider to reduce count of recent turns log after making log detail page.

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
        $user = \Auth::user();
        $userIsland = $user?->island;
        $getLogRecentTurns = config('app.hakoniwa.detail_page_show_log_turns');

        $islandPlans = $island->islandPlans()->where('turn_id', $turn->id)->firstOrFail();
        /** @var IslandStatus $islandStatus */
        $islandStatus = $island->islandStatuses()->where('turn_id', $turn->id)->firstOrFail();
        /** @var IslandTerrain $islandTerrain */
        $islandTerrain = $island->islandTerrains()->where('turn_id', $turn->id)->firstOrFail();
        $islandComment = $island->islandComments()->first();
        $islandAchievements = $island->islandAchievements()->with(['island', 'turn' => function ($query) { $query->withTrashed(); }])->get();
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

        $islandBbses = IslandBbs::where('island_id', $islandId)
            ->withTrashed()
            ->orderByDesc('id')
            ->limit(config('app.hakoniwa.default_show_bbs_comments'))
            ->with(['commenterIsland', 'turn' => function ($query) { $query->withTrashed(); }])
            ->get();

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
                'comment' => $islandComment->comment ?? null,
                'status' => [
                    'development_points' => $islandStatus->development_points,
                    'funds' => $islandStatus->funds,
                    'foods' => $islandStatus->foods,
                    'resources' => $islandStatus->resources,
                    'population' => $islandStatus->population,
                    'funds_production_capacity' => $islandStatus->funds_production_capacity,
                    'foods_production_capacity' => $islandStatus->foods_production_capacity,
                    'resources_production_capacity' => $islandStatus->resources_production_capacity,
                    'maintenance_number_of_people' => $islandStatus->maintenance_number_of_people,
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
                'achievements' => Achievements::create()->fromModel($islandAchievements)->toArray(),
                'bbs' => $islandBbses->map(function ($islandBbs) use ($user, $userIsland) {
                    /** @var IslandBbs $islandBbs */
                    return $islandBbs->toViewArray($islandBbs->turn, $islandBbs->commenterIsland, $user, $userIsland);
                }),
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
