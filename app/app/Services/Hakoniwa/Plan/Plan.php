<?php

namespace App\Services\Hakoniwa\Plan;

use App\Services\Hakoniwa\Cell\City;
use App\Services\Hakoniwa\Cell\Factory;
use App\Services\Hakoniwa\Cell\Farm;
use App\Services\Hakoniwa\Cell\FarmDome;
use App\Services\Hakoniwa\Cell\Forest;
use App\Services\Hakoniwa\Cell\Lake;
use App\Services\Hakoniwa\Cell\LargeFactory;
use App\Services\Hakoniwa\Cell\Metropolis;
use App\Services\Hakoniwa\Cell\Mine;
use App\Services\Hakoniwa\Cell\MissileBase;
use App\Services\Hakoniwa\Cell\MonumentOfAgriculture;
use App\Services\Hakoniwa\Cell\MonumentOfMaster;
use App\Services\Hakoniwa\Cell\MonumentOfMining;
use App\Services\Hakoniwa\Cell\MonumentOfPeace;
use App\Services\Hakoniwa\Cell\MonumentOfWar;
use App\Services\Hakoniwa\Cell\MonumentOfWinner;
use App\Services\Hakoniwa\Cell\Mountain;
use App\Services\Hakoniwa\Cell\Oilfield;
use App\Services\Hakoniwa\Cell\Park;
use App\Services\Hakoniwa\Cell\Plain;
use App\Services\Hakoniwa\Cell\Sea;
use App\Services\Hakoniwa\Cell\SeabedBase;
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
    public const USE_AMOUNT = false;
    public const USE_TARGET_ISLAND = false;
    public const EXECUTABLE_DEVELOPMENT_POINT = 0;

    protected string $key = self::KEY;
    protected string $name = self::NAME;
    protected int $price = self::PRICE;
    protected string $priceString = self::PRICE_STRING;
    protected bool $usePoint = self::USE_POINT;
    protected bool $useAmount = self::USE_AMOUNT;
    protected bool $useTargetIsland = self::USE_TARGET_ISLAND;
    protected int $executableDevelopmentPoint = self::EXECUTABLE_DEVELOPMENT_POINT;

    protected int $amount;
    protected Point $point;

    public const GRADABLE_CELLS = [
        Metropolis::TYPE,
        City::TYPE,
        Factory::TYPE,
        LargeFactory::TYPE,
        Farm::TYPE,
        FarmDome::TYPE,
        Forest::TYPE,
        Oilfield::TYPE,
        Town::TYPE,
        Village::TYPE,
        Wasteland::TYPE,
        MissileBase::TYPE,
        Park::TYPE,
        MonumentOfAgriculture::TYPE,
        MonumentOfMining::TYPE,
        MonumentOfMaster::TYPE,
        MonumentOfPeace::TYPE,
        MonumentOfWar::TYPE,
        MonumentOfWinner::TYPE,
    ];

    public const REMOVABLE_CELLS = [
        Factory::TYPE,
        LargeFactory::TYPE,
        Farm::TYPE,
        FarmDome::TYPE,
        Oilfield::TYPE,
        Mine::TYPE,
        MissileBase::TYPE,
        SeabedBase::TYPE,
        Park::TYPE,
        MonumentOfAgriculture::TYPE,
        MonumentOfMining::TYPE,
        MonumentOfMaster::TYPE,
        MonumentOfPeace::TYPE,
        MonumentOfWar::TYPE,
        MonumentOfWinner::TYPE,
    ];

    public const CONSTRUCTABLE_CELLS = [
        Plain::TYPE,
        Metropolis::TYPE,
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

    public function useAmount(): bool
    {
        return $this->useAmount;
    }

    public function useTargetIsland(): bool
    {
        return $this->useTargetIsland;
    }

    public function getExecutableDevelopmentPoint(): int
    {
        return $this->executableDevelopmentPoint;
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
                'useAmount' => $this->useAmount(),
                'useTargetIsland' => $this->useTargetIsland(),
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
