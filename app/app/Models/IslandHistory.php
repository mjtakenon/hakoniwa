<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IslandHistory extends Model
{
    use HasFactory;

    const UPDATED_AT = null;

    public static function createFromIsland(Island $island): static
    {
        $islandHistory = new self();
        $islandHistory->island_id = $island->id;
        $islandHistory->user_id = $island->user_id;
        $islandHistory->name = $island->name;
        $islandHistory->owner_name = $island->owner_name;
        $islandHistory->deleted_at = $island->deleted_at;
        return $islandHistory;
    }
}
