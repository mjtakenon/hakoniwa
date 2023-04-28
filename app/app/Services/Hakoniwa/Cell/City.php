<?php

namespace App\Services\Hakoniwa\Cell;

use App\Models\Island;
use App\Services\Hakoniwa\Status\DevelopmentPointsConst;
use App\Services\Hakoniwa\Status\Status;
use App\Services\Hakoniwa\Terrain\Terrain;
use League\CommonMark\Environment\Environment;

class City extends HasPopulation
{
    public const IMAGE_PATH = '/img/hakoniwa/hakogif/land5.gif';
    public const TYPE = 'city';
    public const NAME = '都市';

    public const MIN_POPULATION = 10000;
    public const MAX_POPULATION = 20000;

    public function __construct(...$data)
    {
        parent::__construct(...$data);
        $this->imagePath = self::IMAGE_PATH;
        $this->type = self::TYPE;

        if (array_key_exists('population', $data)) {
            $this->population = $data['population'];
        } else {
            $this->population = self::MIN_POPULATION;
        }
    }

    protected function getName(): string
    {
        return self::NAME;
    }
}
