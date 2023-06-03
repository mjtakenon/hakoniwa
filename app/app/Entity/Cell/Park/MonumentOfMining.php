<?php

namespace App\Entity\Cell\Park;

use App\Entity\Achievement\Achievements;
use App\Entity\Cell\PassTurnResult;
use App\Entity\Log\Logs;
use App\Entity\Status\Status;
use App\Entity\Terrain\Terrain;
use App\Models\Island;
use App\Models\Turn;
use Illuminate\Support\Collection;

class MonumentOfMining extends Park
{
    public const IMAGE_PATH = '/img/hakoniwa/hakogif/monument52.gif';
    public const TYPE = 'monument_of_mining';
    public const NAME = '鉱の碑';
    public const PRODUCT_DEVELOPMENT_POINTS = 250;
    public const CONSTRUCTABLE_RESOURCES_THRESHOLD = 4000;

    protected string $imagePath = self::IMAGE_PATH;
    protected string $type = self::TYPE;
    protected string $name = self::NAME;

    public static function canBuild(Terrain $terrain, Status $status, Achievements $achievements): bool
    {
        if ($status->getProducedResources() <= self::CONSTRUCTABLE_RESOURCES_THRESHOLD) {
            return false;
        }
        if ($terrain->findByTypes([self::TYPE])->count() >= 1) {
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
