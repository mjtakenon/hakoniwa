<?php

namespace App\Http\Controllers\Api\Islands;

use App\Entity\Plan\Plans;
use App\Http\Controllers\Controller;
use App\Http\Traits\WebApi;
use App\Models\Island;
use App\Models\Turn;

class PlansController extends Controller
{
    use WebApi;
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

        return $this->ok(['plan' => $plans->toArray(true)]);
    }
}
