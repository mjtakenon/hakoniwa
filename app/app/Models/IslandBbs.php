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

    public function toViewArray(?User $user): array
    {
        $data = [
            'id' => $this->id,
            'user_id' => $this->commenterUser->id,
            'visibility' => $this->visibility,
            'deleted' => !is_null($this->deleted_at),
        ];

        if (!is_null($this->deleted_at)) {
            return $data;
        }

        if ($this->visibility === IslandBbs::VISIBILITY_PRIVATE) {
            if ($this->island_id !== $user?->id && $this->commenterIsland->id !== $user?->id) {
                return $data;
            }
        }

        $data['turn'] = $this->turn->turn;
        $data['comment'] = $this->comment;

        if (!is_null($this->commenterIsland)) {
            $data['island'] = [
                'id' => $this->commenterIsland->id,
                'name' => $this->commenterIsland->name,
                'owner_name' => $this->commenterIsland->owner_name,
            ];
        }

        return $data;
    }
}
