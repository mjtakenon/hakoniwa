<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IslandTerrain extends Model
{
    use HasFactory;

    private function generateInitialTerrain (Island $island) {
        $terrain = \Terrain::initTerrain()->toJson();
    }
}
