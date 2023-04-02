<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IslandStatus extends Model
{
    use HasFactory;

    const UPDATED_AT = null;

    const INITIAL_DEVELOPMENT_POINTS = 0;
    const INITIAL_FUNDS = 1000;
    const INITIAL_FOODS = 10000;
    const INITIAL_RESOURCES = 0;

    const ENVIRONMENT_NORMAL = 'normal';
    const ENVIRONMENT_GOOD = 'good';
    const ENVIRONMENT_BEST = 'best';

    public function setInitialStatus(IslandTerrain $islandTerrain) {
        $this->development_points = IslandStatus::INITIAL_DEVELOPMENT_POINTS;
        $this->funds = IslandStatus::INITIAL_FUNDS;
        $this->foods = IslandStatus::INITIAL_FOODS;
        $this->resources = IslandStatus::INITIAL_RESOURCES;

        $aggregatedStatus = $islandTerrain->getAggregatedStatus();
        $this->population = $aggregatedStatus->get('popuration');
        $this->funds_production_number_of_people = $aggregatedStatus->get('funds_production_number_of_people');
        $this->foods_production_number_of_people = $aggregatedStatus->get('foods_production_number_of_people');
        $this->resources_production_number_of_people = $aggregatedStatus->get('resources_production_number_of_people');
        $this->environment = $aggregatedStatus->get('environment');
        $this->area = $aggregatedStatus->get('area');
    }
}
