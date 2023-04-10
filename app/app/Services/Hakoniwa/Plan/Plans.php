<?php

namespace App\Services\Hakoniwa\Plan;

use App\Services\Hakoniwa\JsonEncodable;
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
            $plans[] = new (PlanConst::getClassByType($object->key))(new Point($object->data->point->x, $object->data->point->y), $object->data->amount);
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

    public function toJsonWithStatic(): string
    {
        $plans = [];
        /** @var Plan $plan */
        foreach ($this->plans as $plan) {
            $plans[] = $plan->toArrayWithStatic();
        }
        return json_encode($plans);
    }

    private function shift()
    {
        $cashFlowPlan = new CashFlowPlan();
        $this->plans->push($cashFlowPlan);
        return $this->plans->shift();
    }

    public function execute(Terrain $terrain, Status $status): PlanExecuteResult
    {
        while (true) {
            /** @var Plan $plan */
            $plan = $this->shift();
            // TODO: 各コマンド実装
            $planExecuteResult = $plan->execute($terrain, $status);

            $terrain = $planExecuteResult->getTerrain();
            $status = $planExecuteResult->getStatus();

            // 2回以上行動できる場合はループ
            if ($plan->isTurnSpending()) {
                break;
            }
        }
        return new PlanExecuteResult($terrain, $status);
    }
}
