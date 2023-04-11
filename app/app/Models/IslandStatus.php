<?php

namespace App\Models;

use App\Services\Hakoniwa\Status\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IslandStatus extends Model
{
    use HasFactory;

    const UPDATED_AT = null;

    protected $casts = [
        'development_points' => 'integer',
        'population' => 'integer',
        'funds' => 'integer',
        'foods' => 'integer',
        'resources' => 'integer',
        'funds_production_number_of_people' => 'integer',
        'foods_production_number_of_people' => 'integer',
        'resources_production_number_of_people' => 'integer',
        'area' => 'integer',
    ];

    public function toStatus(): Status
    {
        return Status::create()->fromModel($this);
    }
}
