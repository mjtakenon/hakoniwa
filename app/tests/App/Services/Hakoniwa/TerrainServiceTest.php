<?php

namespace Tests\App\Services\Hakoniwa;

use Tests\TestCase;

class TerrainServiceTest extends TestCase
{
    public function testInitTerrain()
    {
        $json = \IslandService::initTerrain()->toJson();
        $terrain = \IslandService::fromJson($json);
        $this->assertTrue(true);
    }
}
