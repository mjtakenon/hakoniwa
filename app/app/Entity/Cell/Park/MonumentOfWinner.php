<?php

namespace App\Entity\Cell\Park;

use App\Entity\Cell\PassTurnResult;
use App\Entity\Log\Logs;
use App\Entity\Status\Status;
use App\Entity\Terrain\Terrain;
use App\Models\Island;
use App\Models\Turn;
use Illuminate\Support\Collection;

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
        if ($terrain->findByTypes([self::TYPE])->count() >= 1) {
            return false;
        }
        // TODO: ぞぎが実装されたら実装する
        return false;
    }

    public function passTurn(Island $island, Terrain $terrain, Status $status, Turn $turn, Collection $foreignIslandOccurEvents): PassTurnResult
    {
        $status->setDevelopmentPoints($status->getDevelopmentPoints() + self::PRODUCT_DEVELOPMENT_POINTS);
        return new PassTurnResult($terrain, $status, Logs::create());
    }
}
