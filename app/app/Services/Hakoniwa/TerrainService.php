<?php

namespace App\Services\Hakoniwa;

use App\Services\Hakoniwa\Cell\Cell;
use App\Services\Hakoniwa\Cell\Sea;
use App\Services\Hakoniwa\Util\Point;
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
        $objs = json_decode($json);
        foreach($objs as $obj) {
            /** @var Cell $cell */
            $cell = Cell::fromJson($obj->class, $obj->data);
            $terrain[$cell->getPoint()->x][$cell->getPoint()->y] = $cell;
        }
        $this->terrain = $terrain;
        return $this;
    }
}
