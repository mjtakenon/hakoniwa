<?php

namespace App\Http\Controllers\Islands;

use App\Http\Controllers\Controller;
use App\Models\Island;
use App\Models\IslandTerrain;
use App\Services\Hakoniwa\Terrain\Terrain;

class PlansController extends Controller
{
    public function get($islandId) {
        $island = Island::find($islandId);

        if (is_null($island) || !is_null($island->deleted_at)) {
            abort(404);
        }

        $turn = \HakoniwaService::getLatestTurn();

        $islandTerrain = IslandTerrain::find(1);
        $islandTerrain->terrain = Terrain::create()->init()->toJson();
        $islandTerrain->save();

        return view('pages.islands.plans', [
            'user' => \Auth::user(),
            'hakoniwa' => json_encode([
                'width' => \HakoniwaService::getMaxWidth(),
                'height' => \HakoniwaService::getMaxHeight(),
            ]),
            'turn' => $turn,
            'island' => $island,
            'islandPlans' => $island->islandPlans->where('turn_id', $turn->id)->first(),
            'islandStatus' => $island->islandStatuses->where('turn_id', $turn->id)->first(),
            'islandTerrain' => $island->islandTerrains->where('turn_id', $turn->id)->first(),
            'islandLog' => $island->islandLogs, // TODO: nターン前から
        ]);
    }

    public function post() {
        return response()->json(['islands/{id}/plans']);
    }
}
