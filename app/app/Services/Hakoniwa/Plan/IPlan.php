<?php

namespace App\Services\Hakoniwa\Plan;

use App\Services\Hakoniwa\Util\Point;

interface IPlan
{
    public function toArray(): array;

    static public function fromJson(string $key, Point $point, int $amount): IPlan;

    public function __construct(Point $point, int $amount);
    public static function create(Point $point, int $amount): static;
    public function execute(Point $point, int $amount): void;

    public function getName(): string;
    public function getKey(): string;
}
