<?php

namespace App\Entity\Plan;

use App\Entity\Cell\Factory;
use App\Entity\Cell\Farm;
use App\Entity\Cell\FarmDome;
use App\Entity\Cell\Forest;
use App\Entity\Cell\HasPopulation\City;
use App\Entity\Cell\HasPopulation\Metropolis;
use App\Entity\Cell\HasPopulation\Town;
use App\Entity\Cell\HasPopulation\Village;
use App\Entity\Cell\LargeFactory;
use App\Entity\Cell\Mine;
use App\Entity\Cell\MissileBase\MissileBase;
use App\Entity\Cell\MissileBase\SeabedBase;
use App\Entity\Cell\Oilfield;
use App\Entity\Cell\Park\MonumentOfAgriculture;
use App\Entity\Cell\Park\MonumentOfMaster;
use App\Entity\Cell\Park\MonumentOfMining;
use App\Entity\Cell\Park\MonumentOfPeace;
use App\Entity\Cell\Park\MonumentOfWar;
use App\Entity\Cell\Park\MonumentOfWinner;
use App\Entity\Cell\Park\Park;
use App\Entity\Cell\Plain;
use App\Entity\Cell\Ship\Battleship;
use App\Entity\Cell\Ship\Submarine;
use App\Entity\Cell\Ship\TransportShip;
use App\Entity\Cell\Wasteland;
use App\Entity\Status\Status;
use App\Entity\Terrain\Terrain;
use App\Entity\Util\Point;
use App\Models\Island;
use App\Models\Turn;
use Illuminate\Support\Collection;

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

    abstract public function execute(Island $island, Terrain $terrain, Status $status, Turn $turn, Collection $foreignIslandTargetedPlans): ExecutePlanResult;
}
