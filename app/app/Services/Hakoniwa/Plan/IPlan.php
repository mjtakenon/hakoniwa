<?php

namespace App\Services\Hakoniwa\Plan;

use App\Models\Island;
use App\Models\IslandPlan;
use App\Models\IslandStatus;
use App\Models\IslandTerrain;
use App\Services\Hakoniwa\Util\Point;

interface IPlan
{
    public function toArray(): array;
    public function toArrayWithStatic(): array;

    static public function fromJson(string $key, Point $point, int $amount): IPlan;

    public function __construct(Point $point, int $amount);
    public static function create(Point $point, int $amount): static;
    public function execute(Island $island, IslandTerrain $islandTerrain, IslandStatus $islandStatus): void;

    public function getName(): string;
    public function getKey(): string;
    public function isTurnSpending(): bool;
}
