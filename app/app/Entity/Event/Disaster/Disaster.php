<?php

namespace App\Entity\Event\Disaster;

use App\Entity\Log\Logs;
use App\Entity\Status\Status;
use App\Entity\Terrain\Terrain;
use App\Models\Island;
use App\Models\Turn;

class Disaster implements IDisaster
{
    public const DISASTERS = [
        Fire::class,
        Earthquake::class,
        Tsunami::class,
        VolcanicEruption::class,
        Typhoon::class,
        Meteorite::class,
        HugeMeteorite::class,
        Riot::class,
        LandSubsidence::class,
        AppearanceMonster::class,
        PirateInvasion::class,
        AppearanceLevinoth::class,
    ];

    public static function occur(Island $island, Terrain $terrain, Status $status, Turn $turn): DisasterResult
    {
        $logs = Logs::create();

        /** @var IDisaster $disaster */
        foreach (self::DISASTERS as $disaster) {
            $disasterResult = $disaster::occur($island, $terrain, $status, $turn);
            $terrain->setCells($disasterResult->getTerrain()->getCells());
            $status = $disasterResult->getStatus();
            $logs->merge($disasterResult->getLogs());
        }

        return new DisasterResult($terrain, $status, $logs);
    }
}
