<?php

namespace App\Http\Controllers\Islands;

use App\Http\Controllers\Controller;
use App\Models\Island;

class DetailController extends Controller
{
    public function get($islandId) {
        $island = Island::find($islandId);

        if (is_null($island) || !is_null($island->deleted_at)) {
            abort(404);
        }

        return view('pages.islands.detail', [
            'user' => \Auth::user(),
            'hakoniwa' => json_encode([
                'width' => \HakoniwaService::getMaxWidth(),
                'height' => \HakoniwaService::getMaxHeight(),
            ]),
            'island' => $island,
            'islandStatus' => $island->islandStatuses->whereNull('deleted_at')->first(),
            'islandTerrain' => $island->islandTerrains->whereNull('deleted_at')->first(),
            'islandLog' => $island->islandLogs, // TODO: nターン前から
        ]);
    }
}
