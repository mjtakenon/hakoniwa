<?php

namespace App\Services\Hakoniwa\Cell\Park;

use App\Models\Island;
use App\Models\Turn;
use App\Services\Hakoniwa\Cell\Cell;
use App\Services\Hakoniwa\Cell\PassTurnResult;
use App\Services\Hakoniwa\Log\Logs;
use App\Services\Hakoniwa\Status\Status;
use App\Services\Hakoniwa\Terrain\Terrain;

class MonumentOfWinner extends Park
{
    public const IMAGE_PATH = '/img/hakoniwa/hakogif/monument22.gif';
    public const TYPE = 'monument_of_winner';
    public const NAME = '制覇の碑';
    public const PRODUCT_DEVELOPMENT_POINTS = 400;

    protected string $imagePath = self::IMAGE_PATH;
    protected string $type = self::TYPE;
    protected string $name = self::NAME;

    public static function canBuild(Terrain $terrain, Status $status): bool
    {
        // TODO: 実装
        if ($terrain->getTerrain()->flatten(1)->filter(function ($cell) {
                return $cell::TYPE === self::TYPE;
            })->count() >= 1) {
            return false;
        }
        return false;
    }

    public function passTurn(Island $island, Terrain $terrain, Status $status, Turn $turn): PassTurnResult
    {
        $status->setDevelopmentPoints($status->getDevelopmentPoints() + self::PRODUCT_DEVELOPMENT_POINTS);
        return new PassTurnResult($terrain, $status, Logs::create());
    }
}