<?php

namespace App\Entity\Cell\Monster;

interface IMonster
{
    public function getAppearancePopulation(): int;
    public function getDisappearancePopulation(): int;
    public function getExperience(): int;
    public function getCorpsePrice(): int;
    public function getHitPoints(): int;
    public function setHitPoints($hitPoints): void;
    public function getDefaultHitPoints(): int;
    public function getDefaultMoveTimes(): int;
    public function isAttackDisabled(): bool;
}
