<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Turn extends Model
{
    use HasFactory;

    const UPDATED_AT = null;

    public static function getLatestTurn() {
        return Turn::orderBy('created_at')->firstOrFail();
    }
}
