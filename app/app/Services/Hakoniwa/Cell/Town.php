<?php

namespace App\Services\Hakoniwa\Cell;

use App\Services\Hakoniwa\Util\Point;

class Town extends Cell
{
    const IMAGE_PATH = '/img/hakoniwa/hakogif/land4.gif';
    const TYPE = 'town';
    const NAME = '町';

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

    static public function fromJson(string $type, Cell|\stdClass $data): Cell
    {
        return new (CellTypeConst::getClassByType($type))(new Point($data->point->x, $data->point->y), $data->population);
    }
}
