<?php

namespace App\Services\Hakoniwa\Plan;

use App\Services\Hakoniwa\Util\Point;

abstract class Plan implements IPlan
{
    public const KEY = '';

    public const NAME = '';
    public const PRICE = 0;
    public const PRICE_STRING = '(+' . self::PRICE . '億円)';

    protected string $key;
    protected string $name;
    protected int $price;
    protected string $priceString;

    protected int $amount;
    protected Point $point;

    public function __construct(Point $point, int $amount)
    {
        $this->key = self::KEY;
        $this->name = self::NAME;
        $this->price = self::PRICE;
        $this->priceString = self::PRICE_STRING;
        $this->point = $point;
        $this->amount = $amount;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPoint(): Point
    {
        return $this->point;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function toArray(): array
    {
        return [
            'key' => $this->getKey(),
            'data' => [
                'name' => $this->getName(),
                'point' => $this->getPoint(),
                'amount' => $this->getAmount(),
            ]
        ];
    }

    static public function fromJson(string $key, Point $point, int $amount): IPlan
    {
        return new (PlanConst::getClassByType($key))($point ,$amount);
    }

    public static function create(Point $point, int $amount): static
    {
        return new static($point, $amount);
    }
}
