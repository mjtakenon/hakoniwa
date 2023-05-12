<?php

namespace App\Services\Hakoniwa\Cell\Park;

use App\Models\Island;
use App\Models\Turn;
use App\Services\Hakoniwa\Cell\PassTurnResult;
use App\Services\Hakoniwa\Log\Logs;
use App\Services\Hakoniwa\Status\Status;
use App\Services\Hakoniwa\Terrain\Terrain;

class MonumentOfAgriculture extends Park
{
    public const IMAGE_PATH = '/img/hakoniwa/hakogif/monument49.gif';
    public const TYPE = 'monument_of_agriculture';
    public const NAME = '農の碑';
    public const PRODUCT_DEVELOPMENT_POINTS = 250;
    public const CONSTRUCTABLE_FOODS_THRESHOLD = 120000;

    protected string $imagePath = self::IMAGE_PATH;
    protected string $type = self::TYPE;
    protected string $name = self::NAME;

    public static function canBuild(Terrain $terrain, Status $status): bool
    {
        if ($status->getProducedFoods() <= self::CONSTRUCTABLE_FOODS_THRESHOLD) {
            return false;
        }
        if ($terrain->getTerrain()->flatten(1)->filter(function ($cell) {
                return $cell::TYPE === self::TYPE;
            })->count() >= 1) {
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
