<?php

namespace App\Http\Controllers\Islands;

use App\Http\Controllers\Controller;
use App\Models\Island;
use App\Models\Turn;
use App\Services\Hakoniwa\Terrain\Terrain;

class DetailController extends Controller
{
    public function get($islandId) {
        \Log::debug(__CLASS__ . ' ' . __METHOD__ . ' ' . __LINE__);
        $island = Island::find($islandId)->firstOrFail();

        \Log::debug(__CLASS__ . ' ' . __METHOD__ . ' ' . __LINE__);
        if (is_null($island) || !is_null($island->deleted_at)) {
            abort(404);
        }

        \Log::debug(__CLASS__ . ' ' . __METHOD__ . ' ' . __LINE__);
        $turn = Turn::latest()->firstOrFail();
        // TODO 直近取得ターンの変数切り出し
        $getLogRecentTurns = 5;

        \Log::debug(__CLASS__ . ' ' . __METHOD__ . ' ' . __LINE__);

//        var_dump(\Js::from(json_encode([
//            'width' => \HakoniwaService::getMaxWidth(),
//            'height' => \HakoniwaService::getMaxHeight(),
//        ])));
//        var_dump(\Js::from($island));
//        var_dump(\Js::from($island->islandStatuses->where('turn_id', $turn->id)->first()));
//        var_dump(\Js::from($island->islandTerrains->where('turn_id', $turn->id)->first()->terrain));
//        var_dump(\Js::from($island->islandLogs()->whereIn('turn_id',
//            Turn::where('turn', '>=', $turn->turn-$getLogRecentTurns)->get('id')
//        )->orderByDesc('id')->get('log')));
        \Log::debug(__CLASS__ . ' ' . __METHOD__ . ' ' . __LINE__);

//        return response()->json();

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
