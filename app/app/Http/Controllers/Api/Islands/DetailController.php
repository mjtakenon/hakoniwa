<?php

namespace App\Http\Controllers\Api\Islands;

use App\Entity\Log\LogVisibility;
use App\Http\Controllers\Api\WebApi;
use App\Http\Controllers\Controller;
use App\Models\Island;
use App\Models\IslandLog;
use App\Models\IslandStatus;
use App\Models\Turn;
use Illuminate\Support\Collection;

class DetailController extends Controller
{
    use WebApi;
    public function get($islandId) {
        /** @var Island $island */
        $island = Island::find($islandId);

        if (is_null($island) || !is_null($island->deleted_at)) {
            return $this->notFound();
        }

        $turn = Turn::latest()->firstOrFail();
        $islandTerrain = $island->islandTerrains->where('turn_id', $turn->id)->firstOrFail();

        return $this->ok([
            'island' => [
                'id' => $island->id,
                'name' => $island->name,
                'owner_name' => $island->owner_name,
                'terrains' => $islandTerrain->toEntity()->toArray(false, true),
            ]
        ]);
    }
}
