<?php

namespace App\Services\Hakoniwa\Plan;

class PlanConst
{
    const PLAN_LIST = [
        GradingPlan::KEY => GradingPlan::class,
        GroundLevelingPlan::KEY => GroundLevelingPlan::class,
        LandfillPlan::KEY => LandfillPlan::class,
        ExcavationPlan::KEY => ExcavationPlan::class,
        AfforestationPlan::KEY => AfforestationPlan::class,
        DeforestationPlan::KEY => DeforestationPlan::class,
        ConstructFarmPlan::KEY => ConstructFarmPlan::class,
        ConstructFactoryPlan::KEY => ConstructFactoryPlan::class,
        ConstructMinePlan::KEY => ConstructMinePlan::class,
        ConstructOilfieldPlan::KEY => ConstructOilfieldPlan::class,
        ConstructMissileBasePlan::KEY => ConstructMissileBasePlan::class,
        ConstructSeabedBasePlan::KEY => ConstructSeabedBasePlan::class,
        RemovalFacility::KEY => RemovalFacility::class,
        CashFlowPlan::KEY => CashFlowPlan::class,
        AttractActivitiesPlan::KEY => AttractActivitiesPlan::class,
        AbandonmentPlan::KEY => AbandonmentPlan::class,
    ];

    static public function getClassByType(string $type)
    {
        return self::PLAN_LIST[$type];
    }

    static public function getPlanList(): array
    {
        return self::PLAN_LIST;
    }
}
