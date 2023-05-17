<?php

namespace App\Entity\Cell\Ship;

abstract class CombatantShip extends Ship
{
    public const DEFAULT_EXPERIENCE = 0;
    public const DEFAULT_DAMAGE = 0;

    protected int $experience = self::DEFAULT_EXPERIENCE;
    protected int $damage = self::DEFAULT_DAMAGE;
    protected ?int $affiliationId = null;
    protected string $affiliationName = '';
    protected int $offensivePower;
    protected int $defencePower;
    protected ?int $returnTurn = null;
    public const DEFAULT_HEAL_PER_TURN = 10;

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
        $arr['data']['return_turn'] = $this->returnTurn;
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

        if (array_key_exists('return_turn', $data)) {
            $this->returnTurn = $data['return_turn'];
        }
    }

    protected function getOffensiveDamage(CombatantShip $target): int
    {
        return $this->getOffensivePower()/2 - $target->getDefencePower()/4 + random_int(0, 5);
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
        return $this->offensivePower + 0.1 * $this->getLevel();
    }

    /**
     * @return int
     */
    public function getDefencePower(): int
    {
        return $this->defencePower + 0.1 * $this->getLevel();
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

    /**
     * @return int|null
     */
    public function getReturnTurn(): ?int
    {
        return $this->returnTurn;
    }

    /**
     * @param int|null $returnTurn
     */
    public function setReturnTurn(?int $returnTurn): void
    {
        $this->returnTurn = $returnTurn;
    }
}
