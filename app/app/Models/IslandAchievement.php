<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperIslandAchievement
 */
class IslandAchievement extends Model
{
    use HasFactory;

    public function island(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Island::class);
    }

    public function turn(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Turn::class);
    }
}
