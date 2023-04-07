<?php

namespace App\Services\Hakoniwa\Terrain;

use App\Models\IslandStatus;
use App\Services\Hakoniwa\Cell\Cell;
use App\Services\Hakoniwa\Cell\Forest;
use App\Services\Hakoniwa\Cell\Mountain;
use App\Services\Hakoniwa\Cell\Plain;
use App\Services\Hakoniwa\Cell\Sea;
use App\Services\Hakoniwa\Cell\Shallow;
use App\Services\Hakoniwa\Cell\Village;
use App\Services\Hakoniwa\Cell\Wasteland;
use App\Services\Hakoniwa\JsonEncodable;
use App\Services\Hakoniwa\Util\Normal;
use App\Services\Hakoniwa\Util\Point;
use Illuminate\Support\Collection;

class Terrain implements JsonEncodable
{
    private Collection $terrain;

    public static function create(): Terrain
    {
        return new static;
    }

    public function toJson(): string
    {
        $terrain = [];
        foreach ($this->terrain as $row) {
            /** @var Cell $cell */
            foreach ($row as $cell) {
                $terrain[] = $cell->toArray();
            }
        }
        return json_encode($terrain);
    }

    public function fromJson(string $json): Terrain
    {
        $terrain = new Collection();
        $objects = json_decode($json);
        foreach ($objects as $object) {
            /** @var Cell $cell */
            $cell = Cell::fromJson($object->type, $object->data);
            $terrain[$cell->getPoint()->x][$cell->getPoint()->y] = $cell;
        }
        $this->terrain = $terrain;
        return $this;
    }

    public function init(): Terrain
    {
        $terrain = new Collection();
        for ($y = 0; $y < \HakoniwaService::getMaxWidth(); $y++) {
            $row = new Collection();
            for ($x = 0; $x < \HakoniwaService::getMaxHeight(); $x++) {
                $row[] = new Sea(new Point($x, $y));
            }
            $terrain[] = $row;
        }

        $n = 0;
        while (true) {
            $x = (int)Normal::normal(\HakoniwaService::getMaxWidth() / 2, \HakoniwaService::getMaxWidth() / 11);
            $y = (int)Normal::normal(\HakoniwaService::getMaxHeight() / 2, \HakoniwaService::getMaxHeight() / 11);

            if ($terrain[$y][$x]->getType() === 'sea') {
                if ($n < 4) {
                    $terrain[$y][$x] = new Forest(new Point($x, $y));
                } else if ($n < 18) {
                    $terrain[$y][$x] = new Wasteland(new Point($x, $y));
                } else if ($n < 19) {
                    $terrain[$y][$x] = new Mountain(new Point($x, $y));
                } else if ($n < 21) {
                    $terrain[$y][$x] = new Village(new Point($x, $y), 1000);
                } else if ($n < 28) {
                    $terrain[$y][$x] = new Plain(new Point($x, $y));
                } else if ($n < 38) {
                    $terrain[$y][$x] = new Shallow(new Point($x, $y));
//                } else if ($n < 39) {
//                    $terrain[$y][$x] = new MissileBase(new Point($x, $y));
                } else {
                    break;
                }
                $n++;
            }
        }

        $this->terrain = $terrain;
        return $this;
    }

    public function getTerrain(): Collection
    {
        return $this->terrain;
    }

    public function setTerrain($terrain)
    {
        $this->terrain = $terrain;
    }

    public function aggregatePopuration(): int
    {
        return 0;
    }

    public function aggregateFundsProductionNumberOfPeople(): int
    {
        return 0;
    }

    public function aggregateFoodsProductionNumberOfPeople(): int
    {
        return 0;
    }

    public function aggregateResourcesProductionNumberOfPeople(): int
    {
        return 0;
    }

    public function getEnvironment(): string
    {
        return IslandStatus::ENVIRONMENT_BEST;
    }

    public function aggregateArea(): int
    {
        return 0;
    }
}
