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

    public function get(int $islandId)
    {
        if (!\HakoniwaService::isIslandRegistered() || \Auth::user()->island->id !== $islandId) {
            abort(403);
        }

        $island = Island::find($islandId);

        if (is_null($island) || !is_null($island->deleted_at)) {
            abort(404);
        }

        $turn = Turn::latest()->firstOrFail();
        // TODO 直近取得ターンの変数切り出し
        $getLogRecentTurns = 5;

        //
//        $islandTerrain = IslandTerrain::find($islandId);
//        $islandTerrain->terrain = Terrain::create()->init()->toJson();
//        $islandTerrain->save();
//
//        $islandStatus = IslandStatus::find($islandId);
//        $islandStatus->setInitialStatus(Terrain::create()->fromJson($islandTerrain->terrain));
//        $islandStatus->save();
//
//        $islandPlan = IslandPlan::find($islandId);
//        $islandPlan->plan = Plans::init()->toJson();
//        $islandPlan->save();
        //

        $islandPlan = $island->islandPlans->where('turn_id', $turn->id)->first();

        return view('pages.islands.plans', [
            'user' => \Auth::user(),
            'hakoniwa' => json_encode([
                'width' => \HakoniwaService::getMaxWidth(),
                'height' => \HakoniwaService::getMaxHeight(),
            ]),
            'island' => $island,
            'islandPlans' => Plans::fromJson($islandPlan->plan)->toJsonWithStatic(),
            'islandStatus' => $island->islandStatuses->where('turn_id', $turn->id)->first(),
            'islandTerrain' => $island->islandTerrains->where('turn_id', $turn->id)->first(),
            'islandLog' => $island->islandLogs()->whereIn('turn_id',
                Turn::where('turn', '>=', $turn->turn-$getLogRecentTurns)->get('id')
            )->orderByDesc('id')->get('log'),
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

        return response()->json(['plan' => $plans->toJsonWithStatic()]);
    }
}
