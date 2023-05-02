<?php

namespace App\Services\Hakoniwa\Plan;

use App\Models\Island;
use App\Models\Turn;
use App\Services\Hakoniwa\JsonEncodable;
use App\Services\Hakoniwa\Log\Logs;
use App\Services\Hakoniwa\Status\Status;
use App\Services\Hakoniwa\Terrain\Terrain;
use App\Services\Hakoniwa\Util\Point;
use Illuminate\Support\Collection;

class Plans implements JsonEncodable
{
    const MAX_PLANS = 30;
    private Collection $plans;

    public function __construct(Collection $plans)
    {
        $this->plans = $plans;
    }

    /**
     * @return Collection
     */
    public function getPlans(): Collection
    {
        return $this->plans;
    }

    /**
     * @param Collection $plans
     */
    public function setPlans(Collection $plans): void
    {
        $this->plans = $plans;
    }

    public static function init(): static
    {
        $plans = new Collection();

        for ($n = 0; $n < self::MAX_PLANS; $n++) {
            $plans[] = new CashFlowPlan();
        }

        return new static($plans);
    }

    public static function fromJson(string $json): static
    {
        $plans = new Collection();
        $objects = json_decode($json);
        foreach ($objects as $object) {
            /** @var Plan $plan */
            $plans[] = new (PlanConst::getClassByType($object->key))(new Point($object->data->point->x, $object->data->point->y), $object->data->amount, $object->data->targetIsland ?? null);
        }
        return new static($plans);
    }

    public function toJson(): string
    {
        $plans = [];
        /** @var Plan $plan */
        foreach ($this->plans as $plan) {
            $plans[] = $plan->toArray();
        }
        return json_encode($plans);
    }

    public function toArrayWithStatic(): array
    {
        $plans = [];
        /** @var Plan $plan */
        foreach ($this->plans as $plan) {
            $plans[] = $plan->toArrayWithStatic();
        }
        return $plans;
    }

    private function shift()
    {
        $cashFlowPlan = new CashFlowPlan();
        $this->plans->push($cashFlowPlan);
        return $this->plans->shift();
    }

    public function execute(Island $island, Terrain $terrain, Status $status, Turn $turn, Collection $foreignIslandTargetedPlans): ExecutePlanResult
    {
        $logs = Logs::create();
        while (true) {
            /** @var Plan $plan */
            $plan = $this->shift();
            $executePlanResult = $plan->execute($island, $terrain, $status, $turn, $foreignIslandTargetedPlans);

            $terrain = $executePlanResult->getTerrain();
            $status = $executePlanResult->getStatus();
            $logs->merge($executePlanResult->getLogs());

            if ($plan->useAmount() && $plan->getAmount() >= 1) {
                $this->plans->prepend($plan);
                $this->plans->pop();
            }

            // 2回以上行動できる場合はループ
            if ($executePlanResult->isTurnSpending()) {
                break;
            }
        }
        return new ExecutePlanResult($terrain, $status, $logs, true);
    }
}
