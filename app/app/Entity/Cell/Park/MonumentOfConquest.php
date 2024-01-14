<?php

namespace App\Entity\Cell\Park;

use App\Entity\Achievement\Achievements;
use App\Entity\Achievement\Prize\ConquestSign;
use App\Entity\Cell\PassTurnResult;
use App\Entity\Log\Logs;
use App\Entity\Status\Status;
use App\Entity\Terrain\Terrain;
use App\Models\Island;
use App\Models\Turn;
use Illuminate\Support\Collection;

class MonumentOfConquest extends Park
{
    public const TYPE = 'monument_of_conquest';
    public const NAME = '制覇の碑';
    public const PRODUCT_DEVELOPMENT_POINTS = 400;

    protected string $type = self::TYPE;
    protected string $name = self::NAME;

    public static function canBuild(Terrain $terrain, Status $status, Achievements $achievements): bool
    {
        if ($terrain->findByTypes([self::TYPE])->count() >= 1) {
            return false;
        }
        if ($achievements->findByTypes([ConquestSign::TYPE])->isEmpty()) {
            return false;
        }
        return true;
    }

    public function passTurn(Island $island, Terrain $terrain, Status $status, Turn $turn, Collection $foreignIslandEvents): PassTurnResult
    {
        $status->setDevelopmentPoints($status->getDevelopmentPoints() + self::PRODUCT_DEVELOPMENT_POINTS);
        return new PassTurnResult($terrain, $status, Logs::create());
    }
}
