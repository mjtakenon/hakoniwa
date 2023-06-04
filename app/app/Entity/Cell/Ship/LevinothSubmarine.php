<?php

namespace App\Entity\Cell\Ship;

use App\Entity\Cell\Cell;
use App\Entity\Cell\CellConst;
use App\Entity\Cell\Others\Sea;
use App\Entity\Cell\Others\Shallow;
use App\Entity\Cell\Others\Wasteland;
use App\Entity\Cell\PassTurnResult;
use App\Entity\Log\LogRow\AttackAndDefeatLog;
use App\Entity\Log\LogRow\AttackLog;
use App\Entity\Log\LogRow\DestructionByShipLog;
use App\Entity\Log\LogRow\DisappearEnemyShipLog;
use App\Entity\Log\Logs;
use App\Entity\Status\Status;
use App\Entity\Terrain\Terrain;
use App\Models\Island;
use App\Models\Turn;
use Illuminate\Support\Collection;
use function DeepCopy\deep_copy;

class LevinothSubmarine extends LevinothBattleship
{
    public const SEA_IMAGE_PATH = '/img/hakoniwa/hakogif/levinoth_submarine_sea.png';
    public const SHALLOW_IMAGE_PATH = '/img/hakoniwa/hakogif/levinoth_submarine_shallow.png';
    public const TYPE = 'levinoth_submarine';
    public const NAME = 'リヴァイノス艦隊潜水艦';
    public const AFFILIATION_ENEMY = -1;
    public const DEFAULT_RETURN_TURN = 20;
    protected ?int $affiliationId = self::AFFILIATION_ENEMY;

    protected string $shallowImagePath = self::SHALLOW_IMAGE_PATH;
    protected string $seaImagePath = self::SEA_IMAGE_PATH;
    protected string $type = self::TYPE;
    protected string $name = self::NAME;
    protected int $offensivePower = 40;
    protected int $defencePower = 20;
}
