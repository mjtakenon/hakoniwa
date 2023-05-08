<?php

namespace App\Services\Hakoniwa\Plan;

use App\Services\Hakoniwa\Cell\Factory;
use App\Services\Hakoniwa\Cell\Farm;
use App\Services\Hakoniwa\Cell\FarmDome;
use App\Services\Hakoniwa\Cell\Forest;
use App\Services\Hakoniwa\Cell\HasPopulation\City;
use App\Services\Hakoniwa\Cell\HasPopulation\Metropolis;
use App\Services\Hakoniwa\Cell\HasPopulation\Town;
use App\Services\Hakoniwa\Cell\HasPopulation\Village;
use App\Services\Hakoniwa\Cell\LargeFactory;
use App\Services\Hakoniwa\Cell\Mine;
use App\Services\Hakoniwa\Cell\MissileBase\MissileBase;
use App\Services\Hakoniwa\Cell\MissileBase\SeabedBase;
use App\Services\Hakoniwa\Cell\Oilfield;
use App\Services\Hakoniwa\Cell\Park\MonumentOfAgriculture;
use App\Services\Hakoniwa\Cell\Park\MonumentOfMaster;
use App\Services\Hakoniwa\Cell\Park\MonumentOfMining;
use App\Services\Hakoniwa\Cell\Park\MonumentOfPeace;
use App\Services\Hakoniwa\Cell\Park\MonumentOfWar;
use App\Services\Hakoniwa\Cell\Park\MonumentOfWinner;
use App\Services\Hakoniwa\Cell\Park\Park;
use App\Services\Hakoniwa\Cell\Plain;
use App\Services\Hakoniwa\Cell\Ship\Battleship;
use App\Services\Hakoniwa\Cell\Ship\Submarine;
use App\Services\Hakoniwa\Cell\Ship\TransportShip;
use App\Services\Hakoniwa\Cell\Wasteland;
use App\Services\Hakoniwa\Util\Point;

abstract class Plan
{
    public const KEY = '';

    public const NAME = '';
    public const PRICE = 0;
    public const PRICE_STRING = '(' . self::PRICE . '億円)';
    public const DEFAULT_AMOUNT_STRING = '';
    public const AMOUNT_STRING = '';
    public const USE_POINT = true;
    public const USE_AMOUNT = false;
    public const USE_TARGET_ISLAND = false;
    public const IS_FIRING = false;
    public const EXECUTABLE_DEVELOPMENT_POINT = 0;

    protected string $key = self::KEY;
    protected string $name = self::NAME;
    protected int $price = self::PRICE;
    protected string $priceString = self::PRICE_STRING;
    protected string $defaultAmountString = self::DEFAULT_AMOUNT_STRING;
    protected string $amountString = self::AMOUNT_STRING;
    protected bool $usePoint = self::USE_POINT;
    protected bool $useAmount = self::USE_AMOUNT;
    protected bool $useTargetIsland = self::USE_TARGET_ISLAND;
    protected bool $isFiring = self::IS_FIRING;
    protected int $executableDevelopmentPoint = self::EXECUTABLE_DEVELOPMENT_POINT;

    protected int $amount = 0;
    protected ?int $targetIsland = null;
    protected ?Point $point = null;

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
        TransportShip::TYPE,
        Battleship::TYPE,
        Submarine::TYPE,
    ];

    public const CONSTRUCTABLE_CELLS = [
        Plain::TYPE,
        Metropolis::TYPE,
        City::TYPE,
        Town::TYPE,
        Village::TYPE,
    ];

    public function __construct(?Point $point = null, int $amount = 0, ?int $targetIsland = null)
    {
        $this->point = $point;
        $this->amount = $amount;
        $this->targetIsland = $targetIsland;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPrice(): int
    {
        return $this->price;
    }

    public function getPoint(): ?Point
    {
        return $this->point;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getTargetIsland(): ?int
    {
        return $this->targetIsland;
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

    public function isFiring(): bool
    {
        return $this->isFiring;
    }
    public function getExecutableDevelopmentPoint(): int
    {
        return $this->executableDevelopmentPoint;
    }

    public function getPriceString(): string
    {
        return $this->priceString;
    }

    public function getAmountString(): string
    {
        return $this->amountString;
    }

    public function getDefaultAmountString(): string
    {
        return $this->defaultAmountString;
    }

    public function isTurnSpending(): bool
    {
        return true;
    }

    public function toArray(bool $withStatic = false): array
    {
        $arr = [
            'key' => $this->getKey(),
        ];

        if ($this->usePoint) {
            $arr['data']['point'] = $this->getPoint();
        }

        if ($this->useAmount) {
            $arr['data']['amount'] = $this->getAmount();
        }

        if ($this->useTargetIsland) {
            $arr['data']['targetIsland'] = $this->getTargetIsland();
        }

        if ($withStatic) {
            $arr['data']['name'] = $this->getName();
            $arr['data']['usePoint'] = $this->usePoint();
            $arr['data']['useAmount'] = $this->useAmount();
            $arr['data']['useTargetIsland'] = $this->useTargetIsland();
            $arr['data']['isFiring'] = $this->isFiring();
            $arr['data']['priceString'] = $this->getPriceString();
            $arr['data']['amountString'] = $this->getAmountString();
            $arr['data']['defaultAmountString'] = $this->getDefaultAmountString();
        }

        return $arr;
    }

    static public function fromJson(string $key, Point $point, int $amount, ?int $targetIsland = null): static
    {
        return new (PlanConst::getClassByType($key))($point, $amount, $targetIsland);
    }

    public static function create(Point $point, int $amount, ?int $targetIsland = null): static
    {
        return new static($point, $amount, $targetIsland);
    }
}
