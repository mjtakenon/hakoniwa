<?php

namespace App\Entity\Achievement;

use App\Models\Turn;
use Illuminate\Support\Collection;

class Achievements
{
    protected Collection $achievements;

    public function fromModel()
    {

    }

    public function add(Achievement $achievement)
    {
        // TODO もし存在していなかったら
        $this->achievements->add($achievement);
    }
}
