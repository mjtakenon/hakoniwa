<?php

namespace App\Http\Controllers\Web\Settings;

use App\Http\Controllers\Api\Islands\DetailController;
use App\Http\Controllers\Controller;
use App\Models\Island;
use App\Models\Turn;
use App\Providers\RouteServiceProvider;

class IndexController extends Controller
{
    public function get()
    {
        // ログインしているが島を保有していない場合は、島登録ページに飛ばす
        if (!\HakoniwaService::isIslandRegistered()) {
            return redirect()->route(RouteServiceProvider::ROUTE_REGISTER);
        }

        $island = Island::find(\Auth::user()->island->id);

        $turn = Turn::latest()->firstOrFail();

        $islandStatus = $island->islandStatuses->where('turn_id', $turn->id)->firstOrFail();

        return view('pages.settings', [
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
                    'funds_production_capacity' => $islandStatus->funds_production_capacity,
                    'foods_production_capacity' => $islandStatus->foods_production_capacity,
                    'resources_production_capacity' => $islandStatus->resources_production_capacity,
                    'environment' => $islandStatus->environment,
                    'area' => $islandStatus->area,
                ],
            ],
            'CHANGE_ISLAND_NAME_PRICE' => DetailController::CHANGE_ISLAND_NAME_PRICE
        ]);
    }
}
