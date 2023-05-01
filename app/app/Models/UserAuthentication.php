<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAuthentication extends Model
{
    use HasFactory;

    const PROVIDER_GOOGLE = 'google';

    protected $fillable = [
        'identifier',
        'provider',
        'user_id'
    ];
}
