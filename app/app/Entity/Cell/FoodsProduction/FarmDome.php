<?php

namespace App\Entity\Cell\FoodsProduction;

use App\Entity\Cell\Cell;
use App\Entity\Cell\CellConst;
use App\Entity\Cell\Others\Lake;
use App\Entity\Cell\PassTurnResult;
use App\Entity\Log\Logs;
use App\Entity\Status\DevelopmentPointsConst;
use App\Entity\Status\Status;
use App\Entity\Terrain\Terrain;
use App\Models\Island;
use App\Models\Turn;
use Illuminate\Support\Collection;

class FarmDome extends Farm
{
    public const IMAGE_PATH = '/img/hakoniwa/hakogif/land7.gif';
    public const TYPE = 'farm_dome';
    public const NAME = '農場ドーム';

    protected string $imagePath = self::IMAGE_PATH;
    protected string $type = self::TYPE;
    protected string $name = self::NAME;
}
