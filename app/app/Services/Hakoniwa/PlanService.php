<?php

namespace App\Services\Hakoniwa;

use App\Entity\Plan\Plan;
use App\Entity\Plan\PlanConst;
use App\Entity\Plan\Plans;
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
                            'amountString' => $plan::AMOUNT_STRING,
                            'defaultAmountString' => $plan::DEFAULT_AMOUNT_STRING,
                            'usePoint' => $plan::USE_POINT,
                            'useAmount' => $plan::USE_AMOUNT,
                            'useTargetIsland' => $plan::USE_TARGET_ISLAND,
                            'isFiring' => $plan::IS_FIRING,
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
