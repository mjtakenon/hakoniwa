<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\IslandLog
 *
 * @property int $id
 * @property int $turn_id
 * @property mixed $log
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|IslandLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|IslandLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|IslandLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|IslandLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IslandLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IslandLog whereLog($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IslandLog whereTurnId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IslandLog whereUpdatedAt($value)
 * @property int $island_id
 * @method static \Illuminate\Database\Eloquent\Builder|IslandLog whereIslandId($value)
 * @mixin \Eloquent
 */
class IslandLog extends Model
{
    use HasFactory;
}
