<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Turn extends Model
{
    use HasFactory;


    protected $casts = [
        'turn' => 'integer',
        'created_at' => 'datetime',
        'next_turn_scheduled_at' => 'datetime',
    ];

    const UPDATED_AT = null;
}
