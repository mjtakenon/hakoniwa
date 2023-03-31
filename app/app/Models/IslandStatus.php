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

    private function setInitialStatus() {
        $this->development_points = IslandStatus::INITIAL_DEVELOPMENT_POINTS;
        $this->funds = IslandStatus::INITIAL_FUNDS;
        $this->foods = IslandStatus::INITIAL_FOODS;
        $this->resources = IslandStatus::INITIAL_RESOURCES;
    }

    private function aggregate(IslandTerrain $islandTerrain) {

    }
}
