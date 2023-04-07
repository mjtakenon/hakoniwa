<?php

namespace App\Services\Hakoniwa\Cell;

use App\Services\Hakoniwa\Util\Point;

class City extends Cell
{
    const IMAGE_PATH = '/img/hakoniwa/hakogif/land4.gif';
    const TYPE = 'city';
    const NAME = '都市';

    public function __construct(Point|\stdClass $point, int $population)
    {
        parent::__construct($point);
        $this->imagePath = self::IMAGE_PATH;
        $this->type = self::TYPE;
        $this->population = $population;
    }

    public function getInfoString(): string
    {
        return
            '('. $this->point->x . ',' . $this->point->y .') ' . self::NAME . PHP_EOL .
            '人口 ' . $this->population . '人';
    }
}
