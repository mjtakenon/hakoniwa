<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\IslandHistory
 *
 * @property int $id
 * @property int $island_id
 * @property string $name
 * @property string $owner_name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|IslandHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|IslandHistory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|IslandHistory query()
 * @method static \Illuminate\Database\Eloquent\Builder|IslandHistory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IslandHistory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IslandHistory whereIslandId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IslandHistory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IslandHistory whereOwnerName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IslandHistory whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class IslandHistory extends Model
{
    use HasFactory;
    
    const UPDATED_AT = null;
}
