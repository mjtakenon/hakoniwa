<?php

namespace App\Services\Hakoniwa\Cell;

use App\Models\Island;
use App\Services\Hakoniwa\Status\Status;
use App\Services\Hakoniwa\Terrain\Terrain;
use App\Services\Hakoniwa\Util\Point;

class Metropolis extends City
{
    public const IMAGE_PATH = '/img/hakoniwa/hakogif/land502.gif';
    public const TYPE = 'metropolis';
    public const NAME = '大都市';
    public const MIN_POPULATION = 20000;
    public const MAX_POPULATION = 30000;

    protected string $imagePath = self::IMAGE_PATH;
    protected string $type = self::TYPE;
    protected string $name = self::NAME;

    public function __construct(...$data)
    {
        parent::__construct(...$data);

        if (array_key_exists('population', $data)) {
            $this->population = $data['population'];
        } else {
            $this->population = self::MIN_POPULATION;
        }
    }
}
