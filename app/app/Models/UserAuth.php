<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAuth extends Model
{
    use HasFactory;

    const PROVIDER_GOOGLE = 'google';

    protected $fillable = [
        'identify',
        'provider',
        'user_id'
    ];
}
