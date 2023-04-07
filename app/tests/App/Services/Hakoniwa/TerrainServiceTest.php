<?php

namespace Tests\App\Services\Hakoniwa;

use App\Services\Hakoniwa\Terrain\Terrain;
use Tests\TestCase;

class TerrainServiceTest extends TestCase
{
    public function testInitTerrain()
    {
        $json = Terrain::create()->toJson();
        $terrain = \IslandService::fromJson($json);
        $this->assertTrue(true);
    }
}
