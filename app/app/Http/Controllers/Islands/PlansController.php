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
        \DB::enableQueryLog();
        \Log::debug(__CLASS__ . ' ' . __METHOD__ . ' ' . __LINE__);
        $island = Island::find($islandId);

        \Log::debug(__CLASS__ . ' ' . __METHOD__ . ' ' . __LINE__);
        if (is_null($island) || !is_null($island->deleted_at)) {
            abort(404);
        }

        \Log::debug(__CLASS__ . ' ' . __METHOD__ . ' ' . __LINE__);
        if (!\HakoniwaService::isIslandRegistered() || \Auth::user()->island->id !== $island->id) {
            abort(403);
        }

        \Log::debug(__CLASS__ . ' ' . __METHOD__ . ' ' . __LINE__);
        $turn = Turn::latest()->firstOrFail();
        // TODO 直近取得ターンの変数切り出し
        $getLogRecentTurns = 5;

        \Log::debug(__CLASS__ . ' ' . __METHOD__ . ' ' . __LINE__);
        //
//        $islandTerrain = Terrain::find($islandId);
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

        \Log::debug(__CLASS__ . ' ' . __METHOD__ . ' ' . __LINE__);
        $user = \Auth::user();
        \Log::debug(__CLASS__ . ' ' . __METHOD__ . ' ' . __LINE__);
        $islandPlans = Plans::fromJson($islandPlan->plan)->toJsonWithStatic();
        \Log::debug(__CLASS__ . ' ' . __METHOD__ . ' ' . __LINE__);
        $islandStatuses = $island->islandStatuses->where('turn_id', $turn->id)->first();
        \Log::debug(__CLASS__ . ' ' . __METHOD__ . ' ' . __LINE__);
        $islandTerrains = $island->islandTerrains->where('turn_id', $turn->id)->first();
        \Log::debug(__CLASS__ . ' ' . __METHOD__ . ' ' . __LINE__);
        $islandLog = $island->islandLogs()->whereIn('turn_id',
            Turn::where('turn', '>=', $turn->turn-$getLogRecentTurns)->get('id')
        )->orderByDesc('id')->get('log');
        \Log::debug(__CLASS__ . ' ' . __METHOD__ . ' ' . __LINE__);

        \Log::debug(print_r(\DB::getQueryLog(),true));
        $view = view('pages.islands.plans', [
            'user' => $user,
            'hakoniwa' => json_encode([
                'width' => \HakoniwaService::getMaxWidth(),
                'height' => \HakoniwaService::getMaxHeight(),
            ]),
            'island' => $island,
            'islandPlans' => $islandPlans,
            'islandStatus' => $islandStatuses,
            'islandTerrain' => $islandTerrains,
            'islandLog' => $islandLog,
        ]);
        \Log::debug(print_r(\DB::getQueryLog(),true));
        \Log::debug(__CLASS__ . ' ' . __METHOD__ . ' ' . __LINE__);
        return $view;
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
