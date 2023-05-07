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
        ConstructFarmDomePlan::KEY => ConstructFarmDomePlan::class,
        ConstructFactoryPlan::KEY => ConstructFactoryPlan::class,
        ConstructLargeFactoryPlan::KEY => ConstructLargeFactoryPlan::class,
        ConstructMinePlan::KEY => ConstructMinePlan::class,
        ConstructOilfieldPlan::KEY => ConstructOilfieldPlan::class,
        ConstructParkPlan::KEY => ConstructParkPlan::class,
        ConstructMissileBasePlan::KEY => ConstructMissileBasePlan::class,
        ConstructSeabedBasePlan::KEY => ConstructSeabedBasePlan::class,
        ConstructTransportShipPlan::KEY => ConstructTransportShipPlan::class,
        RemovalFacility::KEY => RemovalFacility::class,
        FiringMissilePlan::KEY => FiringMissilePlan::class,
        FiringHighAccuracyMissilePlan::KEY => FiringHighAccuracyMissilePlan::class,
        FoodsTransportationPlan::KEY => FoodsTransportationPlan::class,
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
