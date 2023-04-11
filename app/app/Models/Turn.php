<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Turn extends Model
{
    use HasFactory;

    protected $dates = [
        'created_at',
        'next_turn_scheduled_at'
    ];

    protected $casts = [
        'turn' => 'integer',
    ];

    const UPDATED_AT = null;

    public static function getLatest($columns = ['*'])
    {
        return self::latest()->firstOrFail($columns);
    }
}
