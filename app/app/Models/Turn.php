<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin IdeHelperTurn
 */
class Turn extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $casts = [
        'turn' => 'integer',
        'created_at' => 'datetime',
        'deleted_at' => 'datetime',
        'next_turn_scheduled_at' => 'datetime',
    ];

    const UPDATED_AT = null;

    public function islandLogs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(IslandLog::class);
    }

    public function islandPlans(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(IslandPlan::class);
    }

    public function islandStatuses(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(IslandStatus::class);
    }

    public function islandTerrains(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(IslandTerrain::class);
    }
}
