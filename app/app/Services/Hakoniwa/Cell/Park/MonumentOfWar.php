<?php

namespace App\Services\Hakoniwa\Cell\Park;

use App\Models\Island;
use App\Models\Turn;
use App\Services\Hakoniwa\Cell\MissileBase;
use App\Services\Hakoniwa\Cell\PassTurnResult;
use App\Services\Hakoniwa\Log\Logs;
use App\Services\Hakoniwa\Status\Status;
use App\Services\Hakoniwa\Terrain\Terrain;

class MonumentOfWar extends Park
{
    public const IMAGE_PATH = '/img/hakoniwa/hakogif/monument50.gif';
    public const TYPE = 'monument_of_war';
    public const NAME = '戦の碑';
    public const PRODUCT_DEVELOPMENT_POINTS = 500;
    public const CONSTRUCTABLE_BASE_LEVEL = 6;

    protected string $imagePath = self::IMAGE_PATH;
    protected string $type = self::TYPE;
    protected string $name = self::NAME;

    public static function canBuild(Terrain $terrain, Status $status): bool
    {
        if ($terrain->getTerrain()->flatten(1)->filter(function ($cell) {
                return $cell::TYPE === self::TYPE;
            })->count() >= 1) {
            return false;
        }

        $missileBaseCells = $terrain->getTerrain()->flatten(1)->filter(function ($cell) {
            return $cell::TYPE === MissileBase::TYPE;
        });
        /** @var MissileBase $missileBaseCell */
        foreach ($missileBaseCells as $missileBaseCell) {
            if ($missileBaseCell->getLevel() >= self::CONSTRUCTABLE_BASE_LEVEL) {
                return true;
            }
        }
        return false;
    }

    public function passTurn(Island $island, Terrain $terrain, Status $status, Turn $turn): PassTurnResult
    {
        $status->setDevelopmentPoints($status->getDevelopmentPoints() + self::PRODUCT_DEVELOPMENT_POINTS);
        return new PassTurnResult($terrain, $status, Logs::create());
    }
}
