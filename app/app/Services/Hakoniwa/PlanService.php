<?php

namespace App\Services\Hakoniwa;

use App\Services\Hakoniwa\Plan\Plan;
use App\Services\Hakoniwa\Plan\PlanConst;
use App\Services\Hakoniwa\Plan\Plans;
use Illuminate\Support\ServiceProvider;

class PlanService extends ServiceProvider
{
    const MAX_PLANS = 30;

    public function getAllPlans(): array
    {
        return array_map(function ($plan) {
            /** @var Plan $plan */
            return [
                'key' => $plan::KEY,
                'name' => $plan::NAME,
                'price' => $plan::PRICE,
                'priceString' => $plan::PRICE_STRING,
                'usePoint' => $plan::USE_POINT,
            ];
        }, PlanConst::getPlanList());
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
