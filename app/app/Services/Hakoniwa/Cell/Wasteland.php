<?php

namespace App\Services\Hakoniwa\Cell;

use App\Services\Hakoniwa\Util\Point;

class Wasteland extends Cell
{
    const IMAGE_PATH = '/img/hakoniwa/hakogif/land1.gif';
    const TYPE = 'wasteland';
    const NAME = '荒地';

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
