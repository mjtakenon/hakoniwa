<?php

namespace App\Http\Controllers\Islands;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Trait\WebApi;
use App\Models\Island;
use App\Models\IslandLog;
use App\Models\IslandPlan;
use App\Models\IslandStatus;
use App\Models\IslandTerrain;
use App\Models\Turn;
use App\Services\Hakoniwa\Plan\Plans;
use App\Services\Hakoniwa\Terrain\Terrain;

class PlansController extends Controller
{
    use WebApi;

    const DEFAULT_SHOW_LOG_TURNS = 5;

    public function get(int $islandId)
    {
        $island = Island::find($islandId);

        if (is_null($island) || !is_null($island->deleted_at)) {
            abort(404);
        }

        if (!\HakoniwaService::isIslandRegistered() || \Auth::user()->island->id !== $island->id) {
            abort(403);
        }

        $turn = Turn::latest()->firstOrFail();
        // TODO 直近取得ターンの変数切り出し
        $getLogRecentTurns = 5;

//        $islandTerrain = Terrains::find($islandId);
//        $islandTerrain->terrain = Terrains::create()->init()->toJson();
//        $islandTerrain->save();
//
//        $islandStatus = Status::find($islandId);
//        $islandStatus->setInitialStatus(Terrains::create()->fromJson($islandTerrain->terrain));
//        $islandStatus->save();
//
//        $islandPlan = IslandPlan::find($islandId);
//        $islandPlan->plan = Plans::init()->toJson();
//        $islandPlan->save();

        $user = \Auth::user();
        $islandPlans = $island->islandPlans->where('turn_id', $turn->id)->firstOrFail()->plan;
        $islandStatus = $island->islandStatuses->where('turn_id', $turn->id)->firstOrFail();
        $islandTerrain = $island->islandTerrains->where('turn_id', $turn->id)->firstOrFail();
        $islandLogs = $island->islandLogs()->whereIn('turn_id',
            Turn::where('turn', '>=', $turn->turn-$getLogRecentTurns)->get('id')
        )->orderByDesc('id')
        ->get('log');

//        $view = view('pages.islands.plans', [
//            'user' => $user,
//            'hakoniwa' => json_encode([
//                'width' => \HakoniwaService::getMaxWidth(),
//                'height' => \HakoniwaService::getMaxHeight(),
//            ]),
//            'island' => $island,
//            'islandPlans' => $islandPlans,
//            'islandStatus' => $islandStatuses,
//            'islandTerrain' => $islandTerrains,
//            'islandLog' => $islandLog,
//        ]);
//        return $view;

        return view('pages.islands.plans', [
            'hakoniwa' => [
                'width' => \HakoniwaService::getMaxWidth(),
                'height' => \HakoniwaService::getMaxHeight(),
            ],
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
                    'funds_production_number_of_people' => $islandStatus->funds_production_number_of_people,
                    'foods_production_number_of_people' => $islandStatus->foods_production_number_of_people,
                    'resources_production_number_of_people' => $islandStatus->resources_production_number_of_people,
                    'environment' => $islandStatus->environment,
                    'area' => $islandStatus->area,
                ],
                'terrains' => Terrain::fromJson($islandTerrain->terrain)->toArray(),
                'plans' => Plans::fromJson($islandPlans)->toArrayWithStatic(),
                'logs' => $islandLogs
            ],
        ]);
    }

    public function put(int $islandId): \Illuminate\Http\JsonResponse
    {
        $validator = \Validator::make(\Request::all(), [
            'plan' => 'string|required',
        ]);

        if ($validator->fails()) {
            return $this->badRequest();
        }

        if (!\HakoniwaService::isIslandRegistered() || \Auth::user()->island->id !== $islandId) {
            return $this->forbidden();
        }

        $island = Island::find($islandId);

        if (is_null($island) || !is_null($island->deleted_at)) {
            return $this->notFound();
        }

        $validated = $validator->safe()->collect();

        $turn = Turn::latest()->firstOrFail();

        $plan = $validated->get('plan');

        if (!\PlanService::isValidPlans($plan)) {
            return $this->badRequest();
        }

        $plans = Plans::fromJson($plan);

        $islandPlan = $island->islandPlans->where('turn_id', $turn->id)->first();

        $islandPlan->plan = $plans->toJson();
        $islandPlan->save();

        return response()->json(['plan' => $plans->toArrayWithStatic()]);
    }
}
