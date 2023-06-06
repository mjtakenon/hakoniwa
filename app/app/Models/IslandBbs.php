<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin IdeHelperIslandBbs
 */
class IslandBbs extends Model
{
    use HasFactory;
    use SoftDeletes;

    const UPDATED_AT = null;

    public const VISIBILITY_PUBLIC = 'public';
    public const VISIBILITY_PRIVATE = 'private';


    public function island(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Island::class);
    }

    public function commenterUser(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'commenter_user_id');
    }

    public function commenterIsland(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Island::class, 'commenter_island_id');
    }

    public function turn(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Turn::class);
    }
}
