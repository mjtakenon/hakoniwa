<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IslandComment extends Model
{
    use SoftDeletes;
    use HasFactory;

    const UPDATED_AT = null;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function island() {
        return $this->belongsTo(Island::class);
    }
}
