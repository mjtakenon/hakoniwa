<?php

namespace App\Http\Controllers\Web\Register;

use App\Entity\Log\LogRow\IslandFoundLog;
use App\Entity\Plan\Plans;
use App\Entity\Status\Status;
use App\Entity\Terrain\Terrain;
use App\Http\Controllers\Controller;
use App\Models\Island;
use App\Models\IslandLog;
use App\Models\IslandPlan;
use App\Models\IslandStatus;
use App\Models\IslandTerrain;
use App\Models\Turn;
use App\Models\User;
use App\Providers\RouteServiceProvider;

class IndexController extends Controller
{
    public function get()
    {
        if (\HakoniwaService::isIslandRegistered()) {
            return redirect()->route(RouteServiceProvider::ROUTE_HOME);
        }

        return view('pages.register');
    }

    public function post()
    {
        $validator = \Validator::make(\Request::all(), [
            'island_name' => 'string|required|max:32',
            'owner_name' => 'string|required|max:32'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->getMessageBag(), 403);
        }

        if (\HakoniwaService::isIslandRegistered()) {
            return redirect()->route(RouteServiceProvider::ROUTE_HOME);
        }

        $validated = $validator->safe()->collect();

        $island = \DB::transaction(function () use ($validated) {

            // 同時送信による超過登録防止の為、テーブルロックを行っている
            $islandCount = Island::lockForUpdate()->count();

            $maxIslands = config('app.hakoniwa.max_islands');

            if (!is_null($maxIslands) && $islandCount >= $maxIslands) {
                \Redirect::back()->withErrors(['message' => '現在最大登録数を超えているため、登録できません。'])->withInput()->throwResponse();
            }

            if (Island::where('name', $validated->get('island_name'))->exists()) {
                \Redirect::back()->withErrors(['message' => '島名は既に使われています。'])->withInput()->throwResponse();
            }

            if (Island::where('owner_name', $validated->get('owner_name'))->exists()) {
                \Redirect::back()->withErrors(['message' => 'オーナー名は既に使われています。'])->withInput()->throwResponse();
            }

            $island = new Island();
            $island->user_id = \Auth::user()->getAuthIdentifier();
            $island->name = $validated->get('island_name');
            $island->owner_name = $validated->get('owner_name');
            $island->save();

            $turn = Turn::latest()->firstOrFail();

            $terrain = Terrain::create()->init();
            $islandTerrain = new IslandTerrain();
            $islandTerrain->turn_id = $turn->id;
            $islandTerrain->island_id = $island->id;
            $islandTerrain->terrain = $terrain->toJson(true);
            $islandTerrain->save();

            $status = Status::create()->init($terrain);
            $islandStatus = new IslandStatus();
            $islandStatus->turn_id = $turn->id;
            $islandStatus->island_id = $island->id;
            $islandStatus->development_points = $status->getDevelopmentPoints();
            $islandStatus->funds = $status->getFunds();
            $islandStatus->foods = $status->getFoods();
            $islandStatus->resources = $status->getResources();
            $islandStatus->population = $status->getPopulation();
            $islandStatus->funds_production_capacity = $status->getFundsProductionCapacity();
            $islandStatus->foods_production_capacity = $status->getFoodsProductionCapacity();
            $islandStatus->resources_production_capacity = $status->getResourcesProductionCapacity();
            $islandStatus->maintenance_number_of_people = $status->getMaintenanceNumberOfPeople();
            $islandStatus->environment = $status->getEnvironment();
            $islandStatus->area = $status->getArea();
            $islandStatus->abandoned_turn = $status->getAbandonedTurn();
            $islandStatus->save();

            $islandPlan = new IslandPlan();
            $islandPlan->turn_id = $turn->id;
            $islandPlan->island_id = $island->id;
            $islandPlan->plan = Plans::init()->toJson();
            $islandPlan->save();

            $islandFoundLog = new IslandFoundLog($island);

            $islandLog = new IslandLog();
            $islandLog->turn_id = $turn->id;
            $islandLog->island_id = $island->id;
            $islandLog->log = $islandFoundLog->generate();
            $islandLog->visibility = $islandFoundLog->getVisibility();
            $islandLog->save();

            return $island;
        });

        return redirect('/islands/' . $island->id . '/plans');
    }
}
