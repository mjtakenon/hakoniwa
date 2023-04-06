<?php

namespace App\Services\Hakoniwa\Plan;

use App\Services\Hakoniwa\Util\Point;

interface IPlan
{
    public function toArray(): array;

    static public function fromJson(string $class, IPlan|\stdClass $data): IPlan;

    public static function create(): static;
    public function execute(Point $point, int $amount): void;

    public function getName(): string;
    public function getPrice(): string;
    public function getKey(): string;
}
