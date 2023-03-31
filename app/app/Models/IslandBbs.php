<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\IslandBbs
 *
 * @property int $id
 * @property int $turn_id
 * @property int $island_id
 * @property int $contributors_island_id
 * @property string $contents
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|IslandBbs newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|IslandBbs newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|IslandBbs query()
 * @method static \Illuminate\Database\Eloquent\Builder|IslandBbs whereContents($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IslandBbs whereContributorsIslandId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IslandBbs whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IslandBbs whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IslandBbs whereIslandId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IslandBbs whereTurnId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IslandBbs whereUpdatedAt($value)
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|IslandBbs whereDeletedAt($value)
 * @mixin \Eloquent
 */
class IslandBbs extends Model
{
    use HasFactory;
}
