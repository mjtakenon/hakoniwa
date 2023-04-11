<?php

namespace App\Http\Controllers\Islands;

use App\Http\Controllers\Controller;
use App\Models\Island;
use App\Models\Turn;

class DetailController extends Controller
{
    public function get($islandId) {
        $island = Island::find($islandId);

        if (is_null($island) || !is_null($island->deleted_at)) {
            abort(404);
        }

        $turn = Turn::getLatest();
        // TODO 直近取得ターンの変数切り出し
        $getLogRecentTurns = 5;

        return view('pages.islands.detail', [
            'user' => \Auth::user(),
            'hakoniwa' => json_encode([
                'width' => \HakoniwaService::getMaxWidth(),
                'height' => \HakoniwaService::getMaxHeight(),
            ]),
            'island' => $island,
            'islandStatus' => $island->islandStatuses->where('turn_id', $turn->id)->first(),
            'islandTerrain' => $island->islandTerrains->where('turn_id', $turn->id)->first(),
            'islandLog' => $island->islandLogs()->whereIn('turn_id',
                Turn::where('turn', '>=', $turn->turn-$getLogRecentTurns)->get('id')
            )->orderByDesc('id')->get('log'),
        ]);
    }
}
