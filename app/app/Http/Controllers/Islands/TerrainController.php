<?php

namespace App\Http\Controllers\Islands;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Trait\WebApi;
use App\Models\Island;
use App\Models\Turn;

class TerrainController extends Controller
{
    use WebApi;
    public function get($islandId) {
        $island = Island::find($islandId)->first();

        if (is_null($island) || !is_null($island->deleted_at)) {
            return $this->notFound();
        }

        $turn = Turn::latest()->firstOrFail();
        // TODO 直近取得ターンの変数切り出し

        $islandTerrain = $island->islandTerrains->where('turn_id', $turn->id)->firstOrFail();

        return $this->ok([
            'terrain' => $islandTerrain->terrain,
        ]);
    }
}
