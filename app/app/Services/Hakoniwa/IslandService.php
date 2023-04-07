<?php

namespace App\Services\Hakoniwa;

use App\Models\IslandStatus;
use App\Models\IslandTerrain;
use App\Services\Hakoniwa\Cell\Cell;
use App\Services\Hakoniwa\Cell\Forest;
use App\Services\Hakoniwa\Cell\Mountain;
use App\Services\Hakoniwa\Cell\Plain;
use App\Services\Hakoniwa\Cell\Sea;
use App\Services\Hakoniwa\Cell\Shallow;
use App\Services\Hakoniwa\Cell\Village;
use App\Services\Hakoniwa\Cell\Wasteland;
use App\Services\Hakoniwa\Terrain\Terrain;
use App\Services\Hakoniwa\Util\Point;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;

class IslandService extends ServiceProvider
{
    public function getAggregatedStatus(Terrain $terrain): Collection // TODO: 型宣言
    {
        $status = new Collection();

        $status->put('population', $terrain->aggregatePopulation());
        $status->put('funds_production_number_of_people', $terrain->aggregateFundsProductionNumberOfPeople());
        $status->put('foods_production_number_of_people', $terrain->aggregateFoodsProductionNumberOfPeople());
        $status->put('resources_production_number_of_people', $terrain->aggregateResourcesProductionNumberOfPeople());
        $status->put('environment', $terrain->getEnvironment());
        $status->put('area', $terrain->aggregateArea());
        return $status;
    }
}
