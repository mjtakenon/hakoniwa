<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\IslandTerrain
 *
 * @property int $id
 * @property mixed $terrain
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|IslandTerrain newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|IslandTerrain newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|IslandTerrain query()
 * @method static \Illuminate\Database\Eloquent\Builder|IslandTerrain whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IslandTerrain whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IslandTerrain whereTerrain($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IslandTerrain whereUpdatedAt($value)
 * @property int $turn_id
 * @property int $island_id
 * @method static \Illuminate\Database\Eloquent\Builder|IslandTerrain whereIslandId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IslandTerrain whereTurnId($value)
 * @mixin \Eloquent
 */
class IslandTerrain extends Model
{
    use HasFactory;

    private function generateInitialTerrain(Island $island, Turn $turn) {
        // $this->turn_id = 
    }
}
