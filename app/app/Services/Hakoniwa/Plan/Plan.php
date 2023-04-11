<?php

namespace App\Services\Hakoniwa\Plan;

use App\Services\Hakoniwa\Cell\City;
use App\Services\Hakoniwa\Cell\Factory;
use App\Services\Hakoniwa\Cell\Farm;
use App\Services\Hakoniwa\Cell\Forest;
use App\Services\Hakoniwa\Cell\Lake;
use App\Services\Hakoniwa\Cell\Mine;
use App\Services\Hakoniwa\Cell\Mountain;
use App\Services\Hakoniwa\Cell\Oilfield;
use App\Services\Hakoniwa\Cell\Plain;
use App\Services\Hakoniwa\Cell\Sea;
use App\Services\Hakoniwa\Cell\Shallow;
use App\Services\Hakoniwa\Cell\Town;
use App\Services\Hakoniwa\Cell\Village;
use App\Services\Hakoniwa\Cell\Wasteland;
use App\Services\Hakoniwa\Util\Point;

abstract class Plan implements IPlan
{
    public const KEY = '';

    public const NAME = '';
    public const PRICE = 0;
    public const PRICE_STRING = '(+' . self::PRICE . '億円)';
    public const USE_POINT = false;

    protected string $key = self::KEY;
    protected string $name = self::NAME;
    protected int $price = self::PRICE;
    protected string $priceString = self::PRICE_STRING;
    protected bool $usePoint = self::USE_POINT;

    protected int $amount;
    protected Point $point;

    public const GRADABLE_CELLS = [
        City::TYPE,
        Factory::TYPE,
        Farm::TYPE,
        Forest::TYPE,
        Oilfield::TYPE,
        Town::TYPE,
        Village::TYPE,
        Wasteland::TYPE,
    ];

    public const CONSTRUCTABLE_CELLS = [
        Plain::TYPE,
        City::TYPE,
        Town::TYPE,
        Village::TYPE,
    ];

    public function __construct(Point $point, int $amount)
    {
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

    public function usePoint(): bool
    {
        return $this->usePoint;
    }

    public function priceString(): string
    {
        return $this->priceString;
    }

    public function isTurnSpending(): bool
    {
        return true;
    }


    public function toArrayWithStatic(): array
    {
        return [
            'key' => $this->getKey(),
            'data' => [
                'name' => $this->getName(),
                'point' => $this->getPoint(),
                'amount' => $this->getAmount(),
                'usePoint' => $this->usePoint(),
            ]
        ];
    }

    public function toArray(): array
    {
        return [
            'key' => $this->getKey(),
            'data' => [
                'point' => $this->getPoint(),
                'amount' => $this->getAmount(),
            ]
        ];
    }

    static public function fromJson(string $key, Point $point, int $amount): IPlan
    {
        return new (PlanConst::getClassByType($key))($point, $amount);
    }

    public static function create(Point $point, int $amount): static
    {
        return new static($point, $amount);
    }
}
