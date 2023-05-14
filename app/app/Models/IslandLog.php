<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IslandLog extends Model
{
    use HasFactory;

    const UPDATED_AT = null;

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
