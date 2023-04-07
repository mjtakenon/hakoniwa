<?php

namespace App\Services\Hakoniwa\Cell;

use App\Services\Hakoniwa\Util\Point;

class Sea extends Cell
{
    const IMAGE_PATH = '/img/hakoniwa/hakogif/land0.gif';
    const TYPE = 'sea';
    const NAME = '海';

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
