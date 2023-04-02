<?php

namespace App\Services\Hakoniwa\Cell;

use App\Services\Hakoniwa\Util\Point;

class Sea extends Cell
{
    const IMAGE_PATH = '/img/hakoniwa/hakogif/land0.gif';
    const TYPE = 'sea';
    public function __construct(Point|\stdClass $point)
    {
        parent::__construct($point);
        $this->imagePath = self::IMAGE_PATH;
        $this->type = self::TYPE;
    }
}
