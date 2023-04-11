<?php

namespace App\Http\Controllers\Register;

use App\Http\Controllers\Controller;
use App\Models\Island;
use App\Models\IslandLog;
use App\Models\IslandPlan;
use App\Models\IslandStatus;
use App\Models\IslandTerrain;
use App\Models\Turn;
use App\Models\User;
use App\Services\Hakoniwa\Log\IslandFoundLog;
use App\Services\Hakoniwa\Plan\Plans;
use App\Services\Hakoniwa\Status\Status;
use App\Services\Hakoniwa\Terrain\Terrain;

class IndexController extends Controller
{
    public function get()
    {
        if (\HakoniwaService::isIslandRegistered()) {
            return redirect()->route('home');
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
            return redirect()->route('home');
        }

        $validated = $validator->safe()->collect();

        $island = \DB::transaction(function () use ($validated) {
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
            $islandTerrain->terrain = $terrain->toJson();
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
            $islandStatus->funds_production_number_of_people = $status->getFundsProductionNumberOfPeople();
            $islandStatus->foods_production_number_of_people = $status->getFoodsProductionNumberOfPeople();
            $islandStatus->resources_production_number_of_people = $status->getResourcesProductionNumberOfPeople();
            $islandStatus->environment = $status->getEnvironment();
            $islandStatus->area = $status->getArea();
            $islandStatus->save();

            $islandPlan = new IslandPlan();
            $islandPlan->turn_id = $turn->id;
            $islandPlan->island_id = $island->id;
            $islandPlan->plan = Plans::init()->toJson();
            $islandPlan->save();

            $islandLog = new IslandLog();
            $islandLog->turn_id = $turn->id;
            $islandLog->island_id = $island->id;
            $islandLog->log = IslandFoundLog::create($island, $turn)->get();
            $islandLog->save();

            return $island;
        });

        return redirect('/islands/' . $island->id . '/plans');
    }
}
