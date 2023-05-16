<?php

namespace App\Services\Hakoniwa\Cell\Park;

use App\Models\Island;
use App\Models\Turn;
use App\Services\Hakoniwa\Cell\PassTurnResult;
use App\Services\Hakoniwa\Log\Logs;
use App\Services\Hakoniwa\Status\Status;
use App\Services\Hakoniwa\Terrain\Terrain;
use Illuminate\Support\Collection;
class MonumentOfPeace extends Park
{
    public const IMAGE_PATH = '/img/hakoniwa/hakogif/monument51.gif';
    public const TYPE = 'monument_of_peace';
    public const NAME = '平和の碑';
    public const PRODUCT_DEVELOPMENT_POINTS = 500;
    public const CONSTRUCTABLE_POPULATION_THRESHOLD = 500000;

    protected string $imagePath = self::IMAGE_PATH;
    protected string $type = self::TYPE;
    protected string $name = self::NAME;

    public static function canBuild(Terrain $terrain, Status $status): bool
    {
        if ($status->getPopulation() <= self::CONSTRUCTABLE_POPULATION_THRESHOLD) {
            return false;
        }
        if ($terrain->findByTypes([self::TYPE])->count() >= 1) {
            return false;
        }
        return true;
    }

    public function passTurn(Island $island, Terrain $terrain, Status $status, Turn $turn, Collection $foreignIslandOccurEvents): PassTurnResult
    {
        $status->setDevelopmentPoints($status->getDevelopmentPoints() + self::PRODUCT_DEVELOPMENT_POINTS);
        return new PassTurnResult($terrain, $status, Logs::create());
    }
}
