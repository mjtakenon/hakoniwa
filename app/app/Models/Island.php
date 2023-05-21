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
    public function islandStatuses(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(IslandStatus::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orderByDevelopmentPoints(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(IslandStatus::class)
            ->orderBy('development_points', 'desc');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function islandTerrains(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(IslandTerrain::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function islandPlans(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(IslandPlan::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function islandLogs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(IslandLog::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function islandComments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(IslandComment::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
