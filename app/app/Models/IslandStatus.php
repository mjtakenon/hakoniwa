<?php

namespace App\Models;

use App\Entity\Status\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'environment',
        'area',
    ];

    public function toEntity(): Status
    {
        return Status::create()->fromModel($this);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function turn() {
        return $this->belongsTo(Turn::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function island() {
        return $this->belongsTo(Island::class);
    }
}
