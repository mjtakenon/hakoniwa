<?php

namespace App\Entity\Plan;

use App\Entity\Plan\OwnIsland\AbandonmentPlan;
use App\Entity\Plan\OwnIsland\AfforestationPlan;
use App\Entity\Plan\OwnIsland\AttractActivitiesPlan;
use App\Entity\Plan\OwnIsland\CashFlowPlan;
use App\Entity\Plan\OwnIsland\ConstructBattleshipPlan;
use App\Entity\Plan\OwnIsland\ConstructFactoryPlan;
use App\Entity\Plan\OwnIsland\ConstructFarmDomePlan;
use App\Entity\Plan\OwnIsland\ConstructFarmPlan;
use App\Entity\Plan\OwnIsland\ConstructLargeFactoryPlan;
use App\Entity\Plan\OwnIsland\ConstructMinePlan;
use App\Entity\Plan\OwnIsland\ConstructMissileBasePlan;
use App\Entity\Plan\OwnIsland\ConstructOilfieldPlan;
use App\Entity\Plan\OwnIsland\ConstructParkPlan;
use App\Entity\Plan\OwnIsland\ConstructSeabedBasePlan;
use App\Entity\Plan\OwnIsland\ConstructSubmarinePlan;
use App\Entity\Plan\OwnIsland\ConstructTransportShipPlan;
use App\Entity\Plan\OwnIsland\DeforestationPlan;
use App\Entity\Plan\OwnIsland\ExcavationPlan;
use App\Entity\Plan\OwnIsland\FiringHighAccuracyMissilePlan;
use App\Entity\Plan\OwnIsland\FiringMissilePlan;
use App\Entity\Plan\OwnIsland\FoodsTransportationPlan;
use App\Entity\Plan\OwnIsland\FundsTransportationPlan;
use App\Entity\Plan\OwnIsland\GradingPlan;
use App\Entity\Plan\OwnIsland\GroundLevelingPlan;
use App\Entity\Plan\OwnIsland\LandfillPlan;
use App\Entity\Plan\OwnIsland\ReinforceBattleshipPlan;
use App\Entity\Plan\OwnIsland\ReinforceSubmarinePlan;
use App\Entity\Plan\OwnIsland\RemovalFacility;
use App\Entity\Plan\OwnIsland\ResourcesTransportationPlan;

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
        ConstructBattleshipPlan::KEY => ConstructBattleshipPlan::class,
        ConstructSubmarinePlan::KEY => ConstructSubmarinePlan::class,
        RemovalFacility::KEY => RemovalFacility::class,
        FiringMissilePlan::KEY => FiringMissilePlan::class,
        FiringHighAccuracyMissilePlan::KEY => FiringHighAccuracyMissilePlan::class,
        FoodsTransportationPlan::KEY => FoodsTransportationPlan::class,
        FundsTransportationPlan::KEY => FundsTransportationPlan::class,
        ResourcesTransportationPlan::KEY => ResourcesTransportationPlan::class,
        ReinforceBattleshipPlan::KEY => ReinforceBattleshipPlan::class,
        ReinforceSubmarinePlan::KEY => ReinforceSubmarinePlan::class,
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
