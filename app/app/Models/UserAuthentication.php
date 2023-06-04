<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperUserAuthentication
 */
class UserAuthentication extends Model
{
    use HasFactory;

    const PROVIDER_GOOGLE = 'google';
    const PROVIDER_YAHOO = 'yahoo';
    const PROVIDER_DEBUG = 'debug';

    protected $fillable = [
        'identifier',
        'provider',
        'user_id'
    ];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
