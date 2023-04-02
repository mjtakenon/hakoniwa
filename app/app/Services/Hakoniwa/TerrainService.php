<?php

namespace App\Services\Hakoniwa;

use App\Models\IslandStatus;
use App\Services\Hakoniwa\Cell\Cell;
use App\Services\Hakoniwa\Cell\Sea;
use App\Services\Hakoniwa\Util\Point;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;

class TerrainService extends ServiceProvider
{
    const MAX_HEIGHT = 15;
    const MAX_WIDTH = 15;

    private array $terrain;

    public function initTerrain(): TerrainService
    {
        $terrain = array();

        for ($x = 0; $x < self::MAX_WIDTH; $x++) {
            $row = array();
            for ($y = 0; $y < self::MAX_HEIGHT; $y++) {
                $row[] = new Sea(new Point($x, $y));
            }
            $terrain[] = $row;
        }

        $this->terrain = $terrain;
        return $this;
    }

    public function toJson(): string
    {
        $terrain = [];
        foreach ($this->terrain as $row) {
            /** @var Cell $cell */
            foreach($row as $cell) {
                $terrain[] = $cell->toArray();
            }
        }
        return json_encode($terrain);
    }

    public function fromJson(string $json): TerrainService
    {
        $terrain = [];
        $objects = json_decode($json);
        foreach($objects as $object) {
            /** @var Cell $cell */
            $cell = Cell::fromJson($object->class, $object->data);
            $terrain[$cell->getPoint()->x][$cell->getPoint()->y] = $cell;
        }
        $this->terrain = $terrain;
        return $this;
    }

    public function getAggregatedStatus(): Collection //TODO: 型宣言
    {
        $status = new Collection();
        $status->put('popuration', 0);
        $status->put('funds_production_number_of_people', 0);
        $status->put('foods_production_number_of_people', 0);
        $status->put('resources_production_number_of_people', 0);
        $status->put('environment', IslandStatus::ENVIRONMENT_BEST);
        $status->put('area', 0);
        return $status;
    }
}
