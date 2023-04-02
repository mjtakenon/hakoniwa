<?php

namespace App\Http\Controllers\Register;

use App\Http\Controllers\Controller;
use App\Models\Island;
use App\Models\IslandStatus;
use App\Models\IslandTerrain;
use App\Models\Turn;
use App\Models\User;

class IndexController extends Controller
{
    public function get()
    {
        return view('pages.register');
    }

    public function post()
    {
        $validator = \Validator::make(\Request::all(), [
            'island_name' => 'string|required|max:32',
            'owner_name' => 'string|required|max:32'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->getMessageBag());
        }

//        if (\Auth::user()->getAuthIdentifier())

        $validated = $validator->safe()->collect();

        \DB::transaction(function () use ($validated) {
            $island = new Island();
            $island->user_id = \Auth::guard('sanctum')->user()->getAuthIdentifier();
            $island->name = $validated->get('island_name');
            $island->owner_name = $validated->get('owner_name');
            $island->save();

            $turn = Turn::getLatestTurn();

            $islandTerrain = new IslandTerrain();
            $islandTerrain->turn_id = $turn->id;
            $islandTerrain->island_id = $island->id;
            $islandTerrain->generateInitialTerrain($island);
            $islandTerrain->save();

            $islandStatus = new IslandStatus();
            $islandStatus->turn_id = $turn->id;
            $islandStatus->island_id = $island->id;
            $islandStatus->setInitialStatus($islandTerrain);
            $islandStatus->save();
        });

        return redirect()->route('home');//response()->json(['test' => 'ok']);
    }
}
