<?php

namespace App\Services\Hakoniwa\Cell;

use App\Services\Hakoniwa\Util\Point;

class City extends Cell
{
    const IMAGE_PATH = '/img/hakoniwa/hakogif/land4.gif';
    const TYPE = 'city';
    const NAME = '都市';

    public function __construct(...$data)
    {
        parent::__construct(...$data);
        $this->imagePath = self::IMAGE_PATH;
        $this->type = self::TYPE;
        $this->population = $data[0]['population'];
    }

    public function toArray(): array
    {
        return [
            'type' => $this->type,//get_class($this),
            'data' => [
                'point' => $this->point,
                'image_path' => $this->imagePath,
                'info' => $this->getInfoString(),
                'population' => $this->population,
            ]
        ];
    }

    public function getInfoString(): string
    {
        return
            '('. $this->point->x . ',' . $this->point->y .') ' . self::NAME . PHP_EOL .
            '人口 ' . $this->population . '人';
    }
}
