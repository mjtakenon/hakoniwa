<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class IslandTerrain extends Model
{
    use HasFactory;

    protected $visible = [
        'terrain',
    ];
    const UPDATED_AT = null;

}
