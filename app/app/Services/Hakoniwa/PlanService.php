<?php

namespace App\Services\Hakoniwa;

use App\Services\Hakoniwa\Plan\CashFlowPlan;
use App\Services\Hakoniwa\Plan\Plan;
use App\Services\Hakoniwa\Plan\PlanConst;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;

class PlanService extends ServiceProvider implements JsonEncodable
{
    const MAX_PLANS = 30;
    private Collection $plans;

    public function getInitialPlans(): PlanService
    {
        $this->plans = new Collection();

        for ($n = 0; $n < self::MAX_PLANS; $n++) {
            $this->plans[] = new CashFlowPlan();
        }

        return $this;
    }

    public function getAllPlans(): array
    {
        return array_map(function ($plan) {
            /** @var Plan $plan */
            return [
                $plan::create()->getKey() => [
                    'name' => $plan::create()->getName(),
                    'price' => $plan::create()->getPrice(),
                ]
            ];
        }, PlanConst::getPlanList());
    }

    public function toJson(): string
    {
        $plans = [];
        foreach ($this->plans as $plan) {
            $plans[] = $plan->toArray();
        }
        return json_encode($plans);
    }

    public function fromJson(string $json): PlanService
    {
        $plans = new Collection();
        $objects = json_decode($json);
        foreach ($objects as $object) {
            /** @var Plan $plan */
            $plans[] = Plan::fromJson($object->class, $object->data);
        }
        $this->plans = $plans;
        return $this;
    }
}
