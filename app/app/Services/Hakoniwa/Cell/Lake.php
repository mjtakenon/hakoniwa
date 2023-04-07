<?php

namespace App\Services\Hakoniwa\Cell;

use App\Services\Hakoniwa\Util\Point;

class Lake extends Cell
{
    const IMAGE_PATH = '/img/hakoniwa/hakogif/land14.gif';
    const TYPE = 'lake';
    const NAME = '湖';

    public function __construct(...$data)
    {
        parent::__construct(...$data);
        $this->imagePath = self::IMAGE_PATH;
        $this->type = self::TYPE;
    }

    public function getInfoString(): string
    {
        return
            '('. $this->point->x . ',' . $this->point->y .') ' . self::NAME;
    }
}
