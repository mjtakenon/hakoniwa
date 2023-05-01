<?php

// @formatter:off
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * App\Models\Island
 *
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string $owner_name
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\IslandLog> $islandLogs
 * @property-read int|null $island_logs_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\IslandPlan> $islandPlans
 * @property-read int|null $island_plans_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\IslandStatus> $islandStatuses
 * @property-read int|null $island_statuses_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\IslandTerrain> $islandTerrains
 * @property-read int|null $island_terrains_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\IslandStatus> $orderByDevelopmentPoints
 * @property-read int|null $order_by_development_points_count
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Island newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Island newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Island query()
 * @method static \Illuminate\Database\Eloquent\Builder|Island whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Island whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Island whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Island whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Island whereOwnerName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Island whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Island whereUserId($value)
 */
	class Island extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\IslandBbs
 *
 * @property int $id
 * @property int $turn_id
 * @property int $island_id
 * @property int $contributors_island_id
 * @property string $contents
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|IslandBbs newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|IslandBbs newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|IslandBbs query()
 * @method static \Illuminate\Database\Eloquent\Builder|IslandBbs whereContents($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IslandBbs whereContributorsIslandId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IslandBbs whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IslandBbs whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IslandBbs whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IslandBbs whereIslandId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IslandBbs whereTurnId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IslandBbs whereUpdatedAt($value)
 */
	class IslandBbs extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\IslandHistory
 *
 * @property int $id
 * @property int $island_id
 * @property string $name
 * @property string $owner_name
 * @property \Illuminate\Support\Carbon $created_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|IslandHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|IslandHistory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|IslandHistory query()
 * @method static \Illuminate\Database\Eloquent\Builder|IslandHistory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IslandHistory whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IslandHistory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IslandHistory whereIslandId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IslandHistory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IslandHistory whereOwnerName($value)
 */
	class IslandHistory extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\IslandLog
 *
 * @property int $id
 * @property int $turn_id
 * @property int $island_id
 * @property mixed $log
 * @property string $visibility
 * @property \Illuminate\Support\Carbon $created_at
 * @method static \Illuminate\Database\Eloquent\Builder|IslandLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|IslandLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|IslandLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|IslandLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IslandLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IslandLog whereIslandId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IslandLog whereLog($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IslandLog whereTurnId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IslandLog whereVisibility($value)
 */
	class IslandLog extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\IslandPlan
 *
 * @property int $id
 * @property int $turn_id
 * @property int $island_id
 * @property mixed $plan
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|IslandPlan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|IslandPlan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|IslandPlan query()
 * @method static \Illuminate\Database\Eloquent\Builder|IslandPlan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IslandPlan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IslandPlan whereIslandId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IslandPlan wherePlan($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IslandPlan whereTurnId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IslandPlan whereUpdatedAt($value)
 */
	class IslandPlan extends \Eloquent {}
}

namespace App\Models{
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
 * @property string $environment
 * @property int $area
 * @property \Illuminate\Support\Carbon $created_at
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
 */
	class IslandStatus extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\IslandTerrain
 *
 * @property int $id
 * @property int $turn_id
 * @property int $island_id
 * @property mixed $terrain
 * @property \Illuminate\Support\Carbon $created_at
 * @method static \Illuminate\Database\Eloquent\Builder|IslandTerrain newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|IslandTerrain newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|IslandTerrain query()
 * @method static \Illuminate\Database\Eloquent\Builder|IslandTerrain whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IslandTerrain whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IslandTerrain whereIslandId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IslandTerrain whereTerrain($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IslandTerrain whereTurnId($value)
 */
	class IslandTerrain extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Turn
 *
 * @property int $id
 * @property int $turn
 * @property \Illuminate\Support\Carbon $next_turn_scheduled_at
 * @property \Illuminate\Support\Carbon $created_at
 * @method static \Illuminate\Database\Eloquent\Builder|Turn newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Turn newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Turn query()
 * @method static \Illuminate\Database\Eloquent\Builder|Turn whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Turn whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Turn whereNextTurnScheduledAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Turn whereTurn($value)
 */
	class Turn extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \App\Models\Island|null $island
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\UserAuthentication
 *
 * @property int $id
 * @property int $user_id
 * @property string $identifier
 * @property string $provider
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|UserAuthentication newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserAuthentication newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserAuthentication query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserAuthentication whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAuthentication whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAuthentication whereIdentifier($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAuthentication whereProvider($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAuthentication whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAuthentication whereUserId($value)
 */
	class UserAuthentication extends \Eloquent {}
}

