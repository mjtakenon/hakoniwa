<?php

namespace App\Entity\Cell\Ship;

interface ICombatantShip
{
    public function getExperience(): int;
    public function getLevel(): int;
    public function setExperience(int $experience): void;
    public function getDamage(): int;
    public function setDamage(int $damage): void;
    public function getOffensivePower(): int;
    public function getDefencePower(): int;
    public function getAffiliationId(): ?int;
    public function getAffiliationName(): string;
    public function getReturnTurn(): ?int;
    public function setReturnTurn(?int $returnTurn): void;
}
