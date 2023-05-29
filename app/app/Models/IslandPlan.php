<?php

namespace App\Models;

use App\Entity\Plan\Plans;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperIslandPlan
 */
class IslandPlan extends Model
{
    use HasFactory;

    protected $visible = [
        'plan',
    ];

    public function turn(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Turn::class);
    }

    public function island(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Island::class);
    }

    public function toEntity(): Plans
    {
        return Plans::fromJson($this->plan);
    }
}
