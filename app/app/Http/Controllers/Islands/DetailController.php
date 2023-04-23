<?php

namespace App\Http\Controllers\Islands;

use App\Http\Controllers\Controller;
use App\Models\Island;
use App\Models\Turn;
use App\Services\Hakoniwa\Terrain\Terrain;

class DetailController extends Controller
{
    public function get($islandId) {
        $island = Island::find($islandId);

        if (is_null($island) || !is_null($island->deleted_at)) {
            abort(404);
        }

        $turn = Turn::latest()->firstOrFail();
        // TODO 直近取得ターンの変数切り出し
        $getLogRecentTurns = 5;

        $islandStatus = $island->islandStatuses->where('turn_id', $turn->id)->firstOrFail();
        $islandTerrain = $island->islandTerrains->where('turn_id', $turn->id)->firstOrFail();

        return view('pages.islands.detail', [
            'user' => \Auth::user(),
            'hakoniwa' => json_encode([
                'width' => \HakoniwaService::getMaxWidth(),
                'height' => \HakoniwaService::getMaxHeight(),
            ]),
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
                    'funds_production_number_of_people' => $islandStatus->funds_production_number_of_people,
                    'foods_production_number_of_people' => $islandStatus->foods_production_number_of_people,
                    'resources_production_number_of_people' => $islandStatus->resources_production_number_of_people,
                    'environment' => $islandStatus->environment,
                    'area' => $islandStatus->area,
                ],
                'terrains' => $islandTerrain->terrain,
                'logs' => $island->islandLogs()->whereIn('turn_id',
                    Turn::where('turn', '>=', $turn->turn-$getLogRecentTurns)->get('id')
                )->orderByDesc('id')->get('log'),
            ],
        ]);
    }
}
