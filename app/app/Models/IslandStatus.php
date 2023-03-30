<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\IslandStatus
 *
 * @property int $id
 * @property int $turn_id
 * @property int $island_id
 * @property int $development_points
 * @property int $population
 * @property int $funds
 * @property int $foods
 * @property int $resources
 * @property int $funds_production_number_of_people
 * @property int $foods_production_number_of_people
 * @property int $resources_production_number_of_people
 * @property int $environment
 * @property int $area
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|IslandStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|IslandStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|IslandStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder|IslandStatus whereArea($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IslandStatus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IslandStatus whereDevelopmentPoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IslandStatus whereEnvironment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IslandStatus whereFoods($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IslandStatus whereFoodsProductionNumberOfPeople($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IslandStatus whereFunds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IslandStatus whereFundsProductionNumberOfPeople($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IslandStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IslandStatus whereIslandId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IslandStatus wherePopulation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IslandStatus whereResources($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IslandStatus whereResourcesProductionNumberOfPeople($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IslandStatus whereTurnId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IslandStatus whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class IslandStatus extends Model
{
    use HasFactory;
}
