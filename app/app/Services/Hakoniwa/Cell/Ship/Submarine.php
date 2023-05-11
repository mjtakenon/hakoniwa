<?php

namespace App\Services\Hakoniwa\Cell\Ship;

class Submarine extends Battleship
{
    public const MAINTENANCE_NUMBER_OF_PEOPLE = 10000;

    protected int $maintenanceNumberOfPeople = self::MAINTENANCE_NUMBER_OF_PEOPLE;

    public const SEA_IMAGE_PATH = '/img/hakoniwa/hakogif/submarine_sea.png';
    public const SHALLOW_IMAGE_PATH = '/img/hakoniwa/hakogif/submarine_shallow.png';
    public const TYPE = 'submarine';
    public const NAME = '潜水艦';

    protected string $shallowImagePath = self::SHALLOW_IMAGE_PATH;
    protected string $seaImagePath = self::SEA_IMAGE_PATH;
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
