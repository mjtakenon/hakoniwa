<?php

namespace App\Models;

use App\Entity\Terrain\Terrain;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IslandTerrain extends Model
{
    use HasFactory;

    protected $visible = [
        'terrain',
    ];
    const UPDATED_AT = null;

    public function toEntity(): Terrain
    {
        return Terrain::fromJson($this->terrain);
    }
}
