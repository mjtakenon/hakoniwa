<?php

namespace Tests\App\Services\Hakoniwa;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Turn;
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
        \Log::debug(Turn::getLatestTurn()->id);
        $terrain = \Terrain::initTerrain();
        $this->assertTrue(true);
    }
}
