<?php

namespace App\Entity\Cell\Monster;

use App\Entity\Cell\Cell;
use App\Entity\Cell\CellConst;
use App\Entity\Cell\Others\Sea;
use App\Entity\Cell\Others\Shallow;
use App\Entity\Cell\Others\Wasteland;
use App\Entity\Cell\PassTurnResult;
use App\Entity\Cell\Ship\Ship;
use App\Entity\Log\LogRow\DisappearMonsterLog;
use App\Entity\Log\Logs;
use App\Entity\Status\Status;
use App\Entity\Terrain\Terrain;
use App\Entity\Util\Rand;
use App\Models\Island;
use App\Models\Turn;
use Illuminate\Support\Collection;
use Mockery\Generator\StringManipulation\Pass\Pass;
use function DeepCopy\deep_copy;

class Levinoth extends Monster
{
    public const SEA_IMAGE_PATH = '/img/hakoniwa/hakogif/levinoth_sea.png';
    public const SHALLOW_IMAGE_PATH = '/img/hakoniwa/hakogif/levinoth_sea.png';
    public const TYPE = 'levinoth';
    public const NAME = '神獣リヴァイノス';
    public const DEFAULT_HIT_POINTS = 10;
    public const DEFAULT_MOVE_TIMES = 1;
    public const EXPERIENCE = 50;
    public const CORPSE_PRICE = 20000;
    private const LAUNCH_EGG_PROBABILITY = 0.3;

    protected string $imagePath = self::SEA_IMAGE_PATH;
    protected string $type = self::TYPE;
    protected string $name = self::NAME;
    protected int $level = 1;
    protected int $elevation = -2;

    public function __construct(...$data)
    {
        parent::__construct(...$data);

        if (array_key_exists('level', $data)) {
            $this->level = $data['level'];
        }

        if (array_key_exists('elevation', $data)) {
            $this->elevation = $data['elevation'];
        }
    }

    public function toArray(bool $isPrivate = false, bool $withStatic = false): array
    {
        $arr = parent::toArray($isPrivate, $withStatic);
        $arr['data']['level'] = $this->level;
        $arr['data']['elevation'] = $this->elevation;
        return $arr;
    }

    public function getAppearancePopulation(): int
    {
        return MonsterConst::APPEARANCE_POPULATION_LV5;
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

    public function getLevel(): int
    {
        return $this->level;
    }

    public function getImagePath(): string
    {
        return $this->elevation === CellConst::ELEVATION_SHALLOW ? self::SHALLOW_IMAGE_PATH : self::SEA_IMAGE_PATH;
    }

    public function getInfoString(bool $isPrivate = false): string
    {
        return
            '(' . $this->point->x . ',' . $this->point->y . ') ' . $this->getName() . PHP_EOL .
            'レベル' . $this->getLevel() . PHP_EOL .
            '体力 ' . $this->getHitPoints();
    }

    private function move(Terrain $terrain, Cell $beforeCell, Cell $afterCell): Terrain
    {
        /** @var Ship $beforeCellCopy */
        $beforeCellCopy = deep_copy($beforeCell);
        $beforeCell->point = $afterCell->getPoint();
        $beforeCell->elevation = $afterCell->getElevation();
        $terrain->setCell($beforeCell->point, $beforeCell);

        if ($beforeCellCopy->getElevation() === CellConst::ELEVATION_SHALLOW) {
            $terrain->setCell($beforeCellCopy->getPoint(), new Shallow(point: $beforeCellCopy->getPoint()));
        } else {
            $terrain->setCell($beforeCellCopy->getPoint(), new Sea(point: $beforeCellCopy->getPoint()));
        }

        return $terrain;
    }

    private function launchEgg($terrain, $status, $logs): PassTurnResult
    {
        // 卵を飛ばす
        if (self::LAUNCH_EGG_PROBABILITY <= Rand::mt_rand_float()) {
            return new PassTurnResult($terrain, $status, $logs);
        }

        /** @var Cell $cell */
        $cell = $terrain->getCells()->flatten()->random();

        if ($cell->getElevation() === CellConst::ELEVATION_MOUNTAIN) {
            // 山吹き飛び
        } else if ($cell->getElevation() === CellConst::ELEVATION_PLAIN) {
            // 建造物があれば崩壊
        } else {
            // 落下
        }

        return new PassTurnResult($terrain, $status, $logs);
    }

    public function passTurn(Island $island, Terrain $terrain, Status $status, Turn $turn, Collection $foreignIslandEvents): PassTurnResult
    {
        if ($this->remainMoveTimes <= 0) {
            return new PassTurnResult($terrain, $status, Logs::create());
        }

        if ($this->getDisappearancePopulation() > $status->getPopulation()) {
            $logs = Logs::create();
            $logs->add(new DisappearMonsterLog($island, $this));
            $terrain->setCell($this->point, new Wasteland(point: $this->point));
            return new PassTurnResult($terrain, $status, $logs);
        }

        $this->remainMoveTimes -= 1;

        $logs = Logs::create();

        $seaCells = $terrain->findByTypes([Sea::TYPE, Shallow::TYPE]);

        if ($seaCells->count() <= 0) {
            return new PassTurnResult($terrain, $status, $logs);
        }

        /** @var Cell $cell */
        $terrain = $this->move($terrain, $this, $seaCells->random());

        return $this->launchEgg($terrain, $status, $logs);
    }
}
