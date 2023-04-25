<?php

namespace App\Services\Hakoniwa\Cell;

use App\Models\Island;
use App\Services\Hakoniwa\Status\Status;
use App\Services\Hakoniwa\Terrain\Terrain;
use App\Services\Hakoniwa\Util\Point;

interface ICell
{

    public static function create($data);
    public function toArray(bool $isPrivate = false): array;

    public function getPoint(): Point;

    public function getInfoString(bool $isPrivate = false): string;

    public function getPopulation(): int;

    public function getFoodsProductionNumberOfPeople():int;

    public function getFundsProductionNumberOfPeople(): int;

    public function getResourcesProductionNumberOfPeople(): int;

    public function getWoods(): int;

    /**
     * @return int
     */
    public function getMaintenanceNumberOfPeople(): int;

    static public function fromJson(string $type, $data): Cell;

    public function passTime(Island $island, Terrain $terrain, Status $status): void;
}
