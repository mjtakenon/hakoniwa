<?php

namespace App\Services\Hakoniwa\Cell;

use App\Services\Hakoniwa\Util\Point;

class Forest extends Cell
{
    const IMAGE_PATH = '/img/hakoniwa/hakogif/land6.gif';
    const TYPE = 'forest';
    const NAME = '森';

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
