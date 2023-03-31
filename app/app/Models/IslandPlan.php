<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\IslandPlan
 *
 * @property int $id
 * @property mixed $plan
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|IslandPlan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|IslandPlan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|IslandPlan query()
 * @method static \Illuminate\Database\Eloquent\Builder|IslandPlan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IslandPlan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IslandPlan wherePlan($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IslandPlan whereUpdatedAt($value)
 * @property int $turn_id
 * @property int $island_id
 * @method static \Illuminate\Database\Eloquent\Builder|IslandPlan whereIslandId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IslandPlan whereTurnId($value)
 * @mixin \Eloquent
 */
class IslandPlan extends Model
{
    use HasFactory;
}
