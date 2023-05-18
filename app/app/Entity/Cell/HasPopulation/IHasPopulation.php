<?php

namespace App\Entity\Cell\HasPopulation;

interface IHasPopulation
{
    public function getPopulation(): int;

    public function setPopulation($population): void;
}
