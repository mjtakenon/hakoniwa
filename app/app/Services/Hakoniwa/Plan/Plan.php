<?php

namespace App\Services\Hakoniwa\Plan;

use App\Services\Hakoniwa\Util\Point;

abstract class Plan implements IPlan
{
    public const KEY = '';

    public const NAME = '';
    public const COMMAND_NAME = '';

    public function getName(): string
    {
        return self::NAME;
    }

    public function getPrice(): string
    {
        return self::COMMAND_NAME;
    }

    public function getKey(): string
    {
        return self::KEY;
    }

    public function toArray(): array
    {
        return [
            'class' => get_class($this),
            'data' => [
                'name' => $this->getName()
            ]
        ];
    }

    static public function fromJson(string $class, IPlan|\stdClass $data): IPlan
    {
        return new $class();
    }

    public static function create(): static
    {
        return new static();
    }
}
