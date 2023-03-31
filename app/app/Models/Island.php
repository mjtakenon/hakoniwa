<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Island
 *
 * @property int $id
 * @property string $name
 * @property string $owner_name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Island newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Island newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Island query()
 * @method static \Illuminate\Database\Eloquent\Builder|Island whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Island whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Island whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Island whereOwnerName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Island whereUpdatedAt($value)
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|Island whereDeletedAt($value)
 * @mixin \Eloquent
 */
class Island extends Model
{
    use HasFactory;
}
