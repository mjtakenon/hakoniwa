<?php

namespace Tests\App\Services\Hakoniwa;

use Tests\TestCase;

class TerrainServiceTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testInitTerrain()
    {
        $terrain = \Terrain::initTerrain();
        $this->assertTrue(true);
    }
}
