<?php

namespace App\Models;

use App\Entity\Plan\Plans;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IslandPlan extends Model
{
    use HasFactory;

    protected $visible = [
        'plan',
    ];

    public function toEntity(): Plans
    {
        return Plans::fromJson($this->plan);
    }
}
