<?php

namespace App\Services\Hakoniwa\Cell;

class Sea extends Cell
{
    const DEFAULT_IMAGE_PATH = '';
    public function __construct(int $x, int $y)
    {
        parent::__construct($x, $y);
        $this->imagePath = '';
    }
}
