<?php

namespace App\Entity\Cell\Monster;

use App\Entity\Cell\PassTurnResult;
use App\Entity\Log\Logs;
use App\Entity\Status\Status;
use App\Entity\Terrain\Terrain;
use App\Models\Island;
use App\Models\Turn;
use Illuminate\Support\Collection;

class Kujira extends Monster
{
    public const TYPE = 'kujira';
    public const NAME = '怪獣クジラ';
    public const DEFAULT_HIT_POINTS = 3;
    public const DEFAULT_MOVE_TIMES = 1;
    public const EXPERIENCE = 15;
    public const CORPSE_PRICE = 4000;
    private bool $isMetalized;

    protected string $type = self::TYPE;
    protected string $name = self::NAME;

    public function __construct(...$data) {

        if (array_key_exists('is_metalized', $data)) {
            $this->isMetalized = $data['is_metalized'];
        } else {
            $this->isMetalized = false;
        }

        parent::__construct(...$data);
    }

    public function toArray(bool $isPrivate = false, bool $withStatic = false): array
    {
        $arr = parent::toArray($isPrivate, $withStatic);
        $arr['data']['is_metalized'] = $this->isMetalized;
        return $arr;
    }

    public function getSubType(): ?string
    {
        return $this->isMetalized ? 'metalized' : 'default';
    }

    public function isAttackDisabled(): bool
    {
        return $this->isMetalized;
    }

    public function getAppearancePopulation(): int
    {
        return MonsterConst::APPEARANCE_POPULATION_LV3;
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

    public function passTurn(Island $island, Terrain $terrain, Status $status, Turn $turn, Collection $foreignIslandEvents): PassTurnResult
    {
        if (($turn->turn + 1) % 3 === 0) {
            $this->isMetalized = true;
            return new PassTurnResult($terrain, $status, Logs::create());
        } else {
            $this->isMetalized = false;
            return parent::passTurn($island, $terrain, $status, $turn, $foreignIslandEvents);
        }
    }
}
