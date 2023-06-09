<?php

namespace App\Entity\Plan;

use App\Entity\Achievement\Achievements;
use App\Entity\JsonCodable;
use App\Entity\Log\Logs;
use App\Entity\Plan\OwnIsland\CashFlowPlan;
use App\Entity\Status\Status;
use App\Entity\Terrain\Terrain;
use App\Entity\Util\Point;
use App\Models\Island;
use App\Models\Turn;
use Illuminate\Support\Collection;

class Plans implements JsonCodable
{
    const MAX_PLANS = 30;
    private Collection $plans;

    public function __construct(Collection $plans)
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
            $point = null;
            $amount = 0;
            $targetIsland = null;

            if (property_exists($object, 'data')) {
                $point = property_exists($object->data,'point') ? new Point($object->data->point->x, $object->data->point->y) : null;
                $amount = property_exists($object->data,'amount') ? $object->data->amount : 0;
                $targetIsland = property_exists($object->data,'targetIsland') ? $object->data->targetIsland : null;
            }

            $plans[] = new (PlanConst::getClassByType($object->key))($point, $amount, $targetIsland);
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

    public function toArray($withStatic = true): array
    {
        $plans = [];
        /** @var Plan $plan */
        foreach ($this->plans as $plan) {
            $plans[] = $plan->toArray($withStatic);
        }
        return $plans;
    }

    private function shift()
    {
        $cashFlowPlan = new CashFlowPlan();
        $this->plans->push($cashFlowPlan);
        return $this->plans->shift();
    }

    public function execute(Island $island, Terrain $terrain, Status $status, Achievements $achievements, Turn $turn, Collection $foreignIslandTargetedPlans): ExecutePlanResult
    {
        $logs = Logs::create();
        while (true) {
            /** @var Plan $plan */
            $plan = $this->shift();
            $executePlanResult = $plan->execute($island, $terrain, $status, $achievements, $turn, $foreignIslandTargetedPlans);

            $terrain = $executePlanResult->getTerrain();
            $status = $executePlanResult->getStatus();
            $achievements = $executePlanResult->getAchievements();
            $logs->merge($executePlanResult->getLogs());

            if ($plan->getKey() === CashFlowPlan::KEY) {
                $status->setAbandonedTurn($status->getAbandonedTurn() + 1);
            } else {
                $status->setAbandonedTurn(0);
            }

            if ($plan->useAmount() && $plan->getAmount() >= 1) {
                $this->plans->prepend($plan);
                $this->plans->pop();
            }

            // 2回以上行動できる場合はループ
            if ($executePlanResult->isTurnSpending()) {
                break;
            }
        }
        return new ExecutePlanResult($terrain, $status, $logs, $achievements, true);
    }
}
