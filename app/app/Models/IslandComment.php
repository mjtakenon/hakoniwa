<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin IdeHelperIslandComment
 */
class IslandComment extends Model
{
    use SoftDeletes;
    use HasFactory;

    const UPDATED_AT = null;

    public function island(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Island::class);
    }
}
