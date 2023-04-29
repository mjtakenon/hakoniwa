<?php

namespace App\Services\Hakoniwa\Cell\Monster;

use App\Models\Island;
use App\Models\Turn;
use App\Services\Hakoniwa\Cell\Cell;
use App\Services\Hakoniwa\Cell\CellTypeConst;
use App\Services\Hakoniwa\Cell\PassTurnResult;
use App\Services\Hakoniwa\Cell\Wasteland;
use App\Services\Hakoniwa\Log\DestructionByMonsterLog;
use App\Services\Hakoniwa\Log\DisappearMonsterLog;
use App\Services\Hakoniwa\Log\Logs;
use App\Services\Hakoniwa\Plan\ExecutePlanResult;
use App\Services\Hakoniwa\Status\Status;
use App\Services\Hakoniwa\Terrain\Terrain;

class Inora extends Monster
{
    public const IMAGE_PATH = '/img/hakoniwa/hakogif/monster0.gif';
    public const TYPE = 'inora';
    public const NAME = '怪獣いのら';
    public const DEFAULT_HIT_POINTS = 1;
    public const DEFAULT_MOVE_TIMES = 1;
    public const EXPERIENCE = 5;
    public const CORPSE_PRICE = 1500;

    public function getName(): string
    {
        return self::NAME;
    }

    public function getType(): string
    {
        return self::TYPE;
    }

    public function getImagePath(): string
    {
        return self::IMAGE_PATH;
    }

    public function getAppearancePopulation(): int
    {
        return MonsterConst::APPEARANCE_POPULATION_LV1;
    }

    public function getCorpsePrice(): int
    {
        return self::CORPSE_PRICE;
    }

    public function getExperience(): int
    {
        return self::EXPERIENCE;
    }

    public function getDefaultHitPoints(): int
    {
        return self::DEFAULT_HIT_POINTS;
    }

    public function getDefaultMoveTimes(): int
    {
        return self::DEFAULT_MOVE_TIMES;
    }
}
