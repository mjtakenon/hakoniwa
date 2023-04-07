<?php

namespace App\Services\Hakoniwa\Cell;

use App\Services\Hakoniwa\Util\Point;

class Farm extends Cell
{
    const IMAGE_PATH = '/img/hakoniwa/hakogif/land7.gif';
    const TYPE = 'farm';
    const NAME = '農場';

    public function __construct(Point|\stdClass $point)
    {
        parent::__construct($point);
        $this->imagePath = self::IMAGE_PATH;
        $this->type = self::TYPE;
    }

    public function getInfoString(): string
    {
        return
            '('. $this->point->x . ',' . $this->point->y .') ' . self::NAME;
    }
}
