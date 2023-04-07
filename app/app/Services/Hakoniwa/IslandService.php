<?php

namespace App\Services\Hakoniwa;

use App\Models\IslandStatus;
use App\Services\Hakoniwa\Cell\Cell;
use App\Services\Hakoniwa\Cell\Forest;
use App\Services\Hakoniwa\Cell\Mountain;
use App\Services\Hakoniwa\Cell\Plain;
use App\Services\Hakoniwa\Cell\Sea;
use App\Services\Hakoniwa\Cell\Town;
use App\Services\Hakoniwa\Cell\Village;
use App\Services\Hakoniwa\Cell\Wasteland;
use App\Services\Hakoniwa\Util\Point;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;

class IslandService extends ServiceProvider implements JsonEncodable
{
    private Collection $terrain;

    public function initTerrain(): IslandService
    {
        $this->terrain = new Collection();

        for ($y = 0; $y < \HakoniwaService::getMaxWidth(); $y++) {
            $row = new Collection();
            for ($x = 0; $x < \HakoniwaService::getMaxHeight();$x++) {
                $row[] = new Sea(new Point($x, $y));
            }
            $this->terrain[] = $row;
        }

        $n = 0;
        while (true) {
            $x = (int)$this->normal(\HakoniwaService::getMaxWidth()/2, \HakoniwaService::getMaxWidth()/12);
            $y = (int)$this->normal(\HakoniwaService::getMaxHeight()/2, \HakoniwaService::getMaxHeight()/12);

            if ($this->terrain[$y][$x]->getType() === 'sea') {
                if ($n < 4) {
                    $this->terrain[$y][$x] = new Forest(new Point($x, $y));
                } else if ($n < 18) {
                    $this->terrain[$y][$x] = new Wasteland(new Point($x, $y));
                } else if ($n < 19) {
                    $this->terrain[$y][$x] = new Mountain(new Point($x, $y));
                } else if ($n < 21) {
                    $this->terrain[$y][$x] = new Village(new Point($x, $y), 1000);
                } else if ($n < 28) {
                    $this->terrain[$y][$x] = new Plain(new Point($x, $y));
                } else if ($n < 29) {
                    // ミサイル基地
                    //$this->terrain[$y][$x] = new Plain(new Point($x, $y));
                }
                $n++;
            } else {
                continue;
            }

            if ($n >= 28) {
                break;
            }
        }

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

    public function fromJson(string $json): IslandService
    {
        $terrain = new Collection();
        $objects = json_decode($json);
        foreach($objects as $object) {
            /** @var Cell $cell */
            $cell = Cell::fromJson($object->type, $object->data);
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

    private function normal($av, $sd): float {
        $x = mt_rand() / mt_getrandmax();
        $y = mt_rand() / mt_getrandmax();
        return sqrt(-2*log($x))*cos(2*pi()*$y)*$sd+$av;
    }
}
