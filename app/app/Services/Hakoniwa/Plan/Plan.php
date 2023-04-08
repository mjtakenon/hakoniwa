<?php

namespace App\Services\Hakoniwa\Plan;

use App\Services\Hakoniwa\Util\Point;

abstract class Plan implements IPlan
{
    public const KEY = '';

    public const NAME = '';
    public const PRICE = 0;

    protected $key;
    protected $name;
    protected $price;

    public function __construct()
    {
        $this->key = self::KEY;
        $this->name = self::NAME;
        $this->price = self::PRICE;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPrice(): string
    {
        return $this->price;
    }

    public function toArray(): array
    {
        return [
            'class' => get_class($this),
            'data' => [
                'key' => $this->getKey(),
                'name' => $this->getName(),
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
