<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class IslandTerrain extends Model
{
    use HasFactory;

    const UPDATED_AT = null;
    public function generateInitialTerrain(Island $island)
    {
        $this->terrain = \IslandService::initTerrain()->toJson();
    }

    public function getAggregatedStatus(): Collection
    {
        return \IslandService::getAggregatedStatus($this);
    }
}
