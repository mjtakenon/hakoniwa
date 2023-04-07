<?php

namespace App\Services\Hakoniwa\Cell;

use App\Services\Hakoniwa\Util\Point;

class Village extends Cell
{
    const IMAGE_PATH = '/img/hakoniwa/hakogif/land3.gif';
    const TYPE = 'village';
    const NAME = '村';

    public function __construct(Point|\stdClass $point)
    {
        parent::__construct($point);
        $this->imagePath = self::IMAGE_PATH;
        $this->type = self::TYPE;
    }

    public function getInfoString(): string
    {
        return
            '('. $this->point->x . ',' . $this->point->y .') ' . self::NAME . PHP_EOL .
            '人口 ' . $this->population . '人';
    }
}
