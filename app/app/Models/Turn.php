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

    public static function getLatest()
    {
        return self::latest()->firstOrFail();
    }

    public function toViewArray(): array
    {
        return [
            'turn' => $this->turn,
            'nextTurnScheduledAt' => $this->next_turn_scheduled_at,
        ];
    }
}
