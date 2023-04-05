<?php

namespace App\Services\Hakoniwa;

use App\Services\Hakoniwa\Plan\CashFlowPlan;
use App\Services\Hakoniwa\Plan\Plan;
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
