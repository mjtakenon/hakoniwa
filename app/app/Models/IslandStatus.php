<?php

namespace App\Models;

use App\Entity\Status\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperIslandStatus
 */
class IslandStatus extends Model
{
    use HasFactory;

    const UPDATED_AT = null;

    protected $casts = [
        'development_points' => 'integer',
        'population' => 'integer',
        'funds' => 'integer',
        'foods' => 'integer',
        'resources' => 'integer',
        'funds_production_capacity' => 'integer',
        'foods_production_capacity' => 'integer',
        'resources_production_capacity' => 'integer',
        'maintenance_number_of_people' => 'integer',
        'area' => 'integer',
    ];

    protected $visible = [
        'island_id',
        'development_points',
        'population',
        'funds',
        'foods',
        'resources',
        'funds_production_capacity',
        'foods_production_capacity',
        'resources_production_capacity',
        'maintenance_number_of_people',
        'environment',
        'area',
    ];

    public function toEntity(): Status
    {
        return Status::create()->fromModel($this);
    }

    public function turn(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Turn::class);
    }

    public function island(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Island::class);
    }
}
