<?php

namespace App\Entity\Cell\Ship;

class Submarine extends Battleship
{
    public const MAINTENANCE_NUMBER_OF_PEOPLE = 10000;

    protected int $maintenanceNumberOfPeople = self::MAINTENANCE_NUMBER_OF_PEOPLE;

    public const TYPE = 'submarine';
    public const NAME = '潜水艦';

    protected string $type = self::TYPE;
    protected string $name = self::NAME;
    protected int $offensivePower = 40;
    protected int $defencePower = 20;

    public function __construct(...$data)
    {
        parent::__construct(...$data);
        $this->maintenanceNumberOfPeople = self::MAINTENANCE_NUMBER_OF_PEOPLE;
    }
}
