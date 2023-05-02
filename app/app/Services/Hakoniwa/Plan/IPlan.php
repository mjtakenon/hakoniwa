<?php

namespace App\Services\Hakoniwa\Plan;

use App\Models\Island;
use App\Models\Turn;
use App\Services\Hakoniwa\Status\Status;
use App\Services\Hakoniwa\Terrain\Terrain;
use App\Services\Hakoniwa\Util\Point;
use Illuminate\Support\Collection;

interface IPlan
{
    public function toArray(): array;

    public function toArrayWithStatic(): array;

    static public function fromJson(string $key, Point $point, int $amount, ?int $targetIsland = null): IPlan;

    public function __construct(Point $point, int $amount, ?int $targetIsland = null);

    public static function create(Point $point, int $amount, ?int $targetIsland = null): static;

    public function execute(Island $island, Terrain $terrain, Status $status, Turn $turn, Collection $foreignIslandTargetedPlans): ExecutePlanResult;

    public function getName(): string;

    public function getKey(): string;

    public function isTurnSpending(): bool;
}
