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

    public function toViewArray(Turn $turn, ?Island $commenterIsland, ?User $viewerUser, ?Island $viewerIsland): array
    {
        $data = [
            'id' => $this->id,
            'user_id' => $this->commenter_user_id,
            'visibility' => $this->visibility,
            'deleted' => !is_null($this->deleted_at),
        ];

        if (!is_null($this->deleted_at)) {
            return $data;
        }

        if ($this->visibility === IslandBbs::VISIBILITY_PRIVATE) {
            if ($this->island_id !== $viewerIsland?->id && $this->commenter_user_id !== $viewerUser?->id) {
                return $data;
            }
        }

        $data['turn'] = $turn->turn;
        $data['comment'] = $this->comment;

        if (!is_null($commenterIsland)) {
            $data['island'] = [
                'id' => $commenterIsland->id,
                'name' => $commenterIsland->name,
                'owner_name' => $commenterIsland->owner_name,
            ];
        }

        return $data;
    }
}
