<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Island extends Model
{
    use HasFactory;

    protected $visible = [
        'id',
        'name',
        'owner_name',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function islandStatuses() {
        return $this->hasMany(IslandStatus::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orderByDevelopmentPoints() {
        return $this->hasMany(IslandStatus::class)
            ->orderBy('development_points', 'desc');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function islandTerrains() {
        return $this->hasMany(IslandTerrain::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function islandPlans() {
        return $this->hasMany(IslandPlan::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function islandLogs() {
        return $this->hasMany(IslandLog::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user() {
        return $this->belongsTo(User::class);
    }
}
