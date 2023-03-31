<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Turn
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Turn newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Turn newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Turn query()
 * @method static \Illuminate\Database\Eloquent\Builder|Turn whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Turn whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Turn whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Turn extends Model
{
    use HasFactory;
    
    const UPDATED_AT = null;

    public static function getLatestTurn() {
        return Turn::orderBy('created_at')->firstOrFail();
    }
}
