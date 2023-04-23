<?php

namespace App\Http\Controllers\Test;

use App\Http\Controllers\Controller;
use App\Models\Island;
use App\Models\Turn;

class DetailController extends Controller
{
    public function get($id) {
        \Log::debug(__METHOD__ . ' ' . __LINE__);
        $island = Island::find(1)->firstOrFail();
        if (is_null($island) || !is_null($island->deleted_at)) {
            abort(404);
        }
        $turn = Turn::latest()->firstOrFail();
        $getLogRecentTurns = 5;
        \Log::debug(__METHOD__ . ' ' . __LINE__);

        $islandStatus = $island->islandStatuses->where('turn_id', $turn->id)->first();
        $islandTerrain = $island->islandTerrains->where('turn_id', $turn->id)->first();
        $islandLog = $island->islandLogs()->whereIn('turn_id',
            Turn::where('turn', '>=', $turn->turn-$getLogRecentTurns)->get('id')
        )->orderByDesc('id')->first();

        view()->share(
            'islandTerrain', [
                'terrain' => substr($islandTerrain->terrain,0,10000),
            ],
        );

        $view = view('pages.tests.'.$id, [
            'user' => \Auth::user(),
            'hakoniwa' => [
                'width' => \HakoniwaService::getMaxWidth(),
                'height' => \HakoniwaService::getMaxHeight(),
            ],
            'island' => [
                'id' => $island->id,
                'name' => $island->name,
                'owner_name' => $island->owner_name,
            ],
            'islandStatus' => [
                'development_points' => $islandStatus->development_points,
                'funds' => $islandStatus->funds,
                'foods' => $islandStatus->foods,
                'resources' => $islandStatus->resources,
                'population' => $islandStatus->population,
                'funds_production_number_of_people' => $islandStatus->funds_production_number_of_people,
                'foods_production_number_of_people' => $islandStatus->foods_production_number_of_people,
                'resources_production_number_of_people' => $islandStatus->resources_production_number_of_people,
                'environment' => $islandStatus->environment,
                'area' => $islandStatus->area,
            ],
//            'islandTerrain' => [
//                'terrain' => substr($islandTerrain->terrain,0,10000),
//            ],
            'islandLog' => [
                'log' => $islandLog->log,
            ]
        ]);
        \Log::debug(__METHOD__ . ' ' . __LINE__);
        return $view;
    }
}
