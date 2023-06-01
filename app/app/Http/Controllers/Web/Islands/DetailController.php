<?php

namespace App\Http\Controllers\Web\Islands;

use App\Entity\Log\LogConst;
use App\Http\Controllers\Controller;
use App\Models\Island;
use App\Models\IslandLog;
use App\Models\IslandStatus;
use App\Models\Turn;
use Illuminate\Support\Collection;

class DetailController extends Controller
{
    // TODO Consider to reduce count of recent turns log after making log detail page.
    const DEFAULT_SHOW_LOG_TURNS = 20;
    public function get($islandId) {
        $island = Island::find($islandId);

        if (is_null($island) || !is_null($island->deleted_at)) {
            abort(404);
        }

        $turn = Turn::latest()->firstOrFail();
        $getLogRecentTurns = self::DEFAULT_SHOW_LOG_TURNS;

        $islandStatus = $island->islandStatuses()->where('turn_id', $turn->id)->firstOrFail();
        $islandTerrain = $island->islandTerrains()->where('turn_id', $turn->id)->firstOrFail();
        $islandComment = $island->islandComments()->first();
        $islandLogs = $island->islandLogs()
            ->whereIn('turn_id', Turn::where('turn', '>=', $turn->turn - self::DEFAULT_SHOW_LOG_TURNS)->get('id'))
            ->whereIn('visibility', [LogConst::VISIBILITY_GLOBAL, LogConst::VISIBILITY_PUBLIC])
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

        // TODO: テスト用のachivements配列
        $testAchievements = [
            [
                "type" => "turn_prize",
                "hover_text" => "100,200,300",
                "extra_text" => "x3"
            ],
            [
                "type" => "prosperity_prize"
            ],
            [
                "type" => "high_prosperity_prize"
            ],
            [
                "type" => "super_prosperity_prize"
            ],
            [
                "type" => "calamity_prize"
            ],
            [
                "type" => "high_calamity_prize"
            ],
            [
                "type" => "levinoth_hunter",
                "extra_text" => "Lv.33"
            ],
            [
                "type" => "treasure_hunter",
                "extra_text" => "Lv.4"
            ]
        ];

        return view('pages.islands.detail', [
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
                    'environment' => $islandStatus->environment,
                    'area' => $islandStatus->area,
                ],
                'terrains' => $islandTerrain->toEntity()->toArray(false, true),
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
                'achievements' => $testAchievements,
            ]
        ]);
    }
}
