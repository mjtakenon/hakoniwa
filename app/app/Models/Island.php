<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperIsland
 */
class Island extends Model
{
    use HasFactory;

    protected $visible = [
        'id',
        'name',
        'owner_name',
    ];

    public function islandStatuses(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(IslandStatus::class);
    }

    public function islandTerrains(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(IslandTerrain::class);
    }

    public function islandPlans(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(IslandPlan::class);
    }

    public function islandLogs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(IslandLog::class);
    }

    public function islandComments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(IslandComment::class);
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function islandHistories(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(IslandHistory::class);
    }
}
