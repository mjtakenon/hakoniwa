<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class IslandTerrain extends Model
{
    use HasFactory;

    public function generateInitialTerrain(Island $island)
    {
        $this->terrain = \Terrain::initTerrain()->toJson();
    }

    public function getAggregatedStatus(): Collection
    {
        return \Terrain::getAggregatedStatus($this);
    }
}
