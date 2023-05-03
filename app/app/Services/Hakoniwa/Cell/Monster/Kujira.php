<?php

namespace App\Services\Hakoniwa\Cell\Monster;

use App\Models\Island;
use App\Models\Turn;
use App\Services\Hakoniwa\Cell\PassTurnResult;
use App\Services\Hakoniwa\Log\Logs;
use App\Services\Hakoniwa\Status\Status;
use App\Services\Hakoniwa\Terrain\Terrain;

class Kujira extends Monster
{
    public const IMAGE_PATH = '/img/hakoniwa/hakogif/monster6.gif';
    public const METALIZED_IMAGE_PATH = '/img/hakoniwa/hakogif/monster4.gif';
    public const TYPE = 'kujira';
    public const NAME = '怪獣クジラ';
    public const DEFAULT_HIT_POINTS = 3;
    public const DEFAULT_MOVE_TIMES = 1;
    public const EXPERIENCE = 15;
    public const CORPSE_PRICE = 4000;
    private bool $isMetalized;

    protected string $imagePath = self::IMAGE_PATH;
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

    public function toArray(bool $isPrivate = false): array
    {
        return [
            'type' => $this->type,
            'data' => [
                'point' => $this->point,
                'image_path' => $this->imagePath,
                'info' => $this->getInfoString($isPrivate),
                'hit_points' => $this->getHitPoints(),
                'is_metalized' => $this->isMetalized,
            ]
        ];
    }

    public function isAttackDisabled(): bool
    {
        return $this->isMetalized;
    }

    public function getImagePath(): string
    {
        return $this->isMetalized ? self::METALIZED_IMAGE_PATH : self::IMAGE_PATH;
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

    public function passTurn(Island $island, Terrain $terrain, Status $status, Turn $turn): PassTurnResult
    {
        if (($turn->turn + 1) % 3 === 0) {
            $this->isMetalized = true;
            return new PassTurnResult($terrain, $status, Logs::create());
        } else {
            $this->isMetalized = false;
            return parent::passTurn($island, $terrain, $status, $turn);
        }
    }
}
