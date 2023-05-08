<?php

namespace App\Services\Hakoniwa\Cell\Ship;

abstract class CombatantShip extends Ship
{
    public const DEFAULT_EXPERIENCE = 0;
    public const DEFAULT_DAMAGE = 0;
//    public const AFFILIATION_PIRATE = -1;
//    public const AFFILIATION_MONSTER = -2;

    protected int $experience = self::DEFAULT_EXPERIENCE;
    protected int $damage = self::DEFAULT_DAMAGE;
    protected ?int $affiliationId = null;
    protected string $affiliationName = '';
    protected int $offensivePower;
    protected int $defencePower;

    private const EXPERIENCE_TABLE = [
        100 => 6,
        75 => 5,
        45 => 4,
        25 => 3,
        10 => 2,
        0 => 1,
    ];

    public function toArray(bool $isPrivate = false, bool $withStatic = false): array
    {
        $arr = parent::toArray($isPrivate, $withStatic);
        $arr['data']['experience'] = $this->experience;
        $arr['data']['damage'] = $this->damage;
        $arr['data']['affiliation_id'] = $this->affiliationId;
        $arr['data']['affiliation_name'] = $this->affiliationName;
        return $arr;
    }

    public function __construct(...$data)
    {
        parent::__construct(...$data);

        if (array_key_exists('experience', $data)) {
            $this->experience = $data['experience'];
        }

        if (array_key_exists('damage', $data)) {
            $this->damage = $data['damage'];
        }

        if (array_key_exists('affiliation_id', $data)) {
            $this->affiliationId = $data['affiliation_id'];
        }

        if (array_key_exists('affiliation_name', $data)) {
            $this->affiliationName = $data['affiliation_name'];
        }
    }

    /**
     * @return int
     */
    public function getExperience(): int
    {
        return $this->experience;
    }

    public function getLevel(): int
    {
        foreach (self::EXPERIENCE_TABLE as $exp => $level) {
            if ($this->experience >= $exp) {
                return $level;
            }
        }
        return 1;
    }

    /**
     * @param int $experience
     */
    public function setExperience(int $experience): void
    {
        $this->experience = $experience;
    }

    /**
     * @return int
     */
    public function getDamage(): int
    {
        return $this->damage;
    }

    /**
     * @param int $damage
     */
    public function setDamage(int $damage): void
    {
        $this->damage = $damage;
    }

    /**
     * @return int
     */
    public function getOffensivePower(): int
    {
        return $this->offensivePower;
    }

    /**
     * @return int
     */
    public function getDefencePower(): int
    {
        return $this->defencePower;
    }

    /**
     * @return int|null
     */
    public function getAffiliationId(): ?int
    {
        return $this->affiliationId;
    }

    /**
     * @return string
     */
    public function getAffiliationName(): string
    {
        return $this->affiliationName;
    }
}
