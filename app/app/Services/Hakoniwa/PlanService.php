<?php

namespace App\Services\Hakoniwa;

use App\Services\Hakoniwa\Plan\Plan;
use App\Services\Hakoniwa\Plan\PlanConst;
use App\Services\Hakoniwa\Plan\Plans;
use Illuminate\Support\ServiceProvider;

class PlanService extends ServiceProvider
{
    const MAX_PLANS = 30;

    public function getExecutablePlans($developmentPoint): array
    {
        return
            array_filter(
                array_map(function ($plan) use ($developmentPoint) {
                    /** @var Plan $plan */
                    if ($developmentPoint >= $plan::EXECUTABLE_DEVELOPMENT_POINT) {
                        return [
                            'key' => $plan::KEY,
                            'name' => $plan::NAME,
                            'price' => $plan::PRICE,
                            'priceString' => $plan::PRICE_STRING,
                            'usePoint' => $plan::USE_POINT,
                            'useAmount' => $plan::USE_AMOUNT,
                            'isFiring' => $plan::IS_FIRING,
                            'useTargetIsland' => $plan::USE_TARGET_ISLAND,
                        ];
                    }
                },
                PlanConst::getPlanList()),
            function ($plan) {
                return !is_null($plan);
            }
        )
        ;
    }

    public function isValidPlans(string $plans): bool
    {
        try {
            Plans::fromJson($plans);
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return false;
        }
        return true;
    }
}
