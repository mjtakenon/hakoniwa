<?php

namespace Tests\App\Http\Controllers\Web\Register;

use App\Models\Island;
use App\Models\IslandPlan;
use App\Models\IslandStatus;
use App\Models\IslandTerrain;
use App\Models\User;
use Tests\TestCase;

class IndexControllerTest extends TestCase
{
    public function testGetSuccess()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)
            ->get('/register');
        $response->assertOk();
    }

    public function testPostSuccess()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)
            ->post('/register', [
            'island_name' => 'test_island_name',
            'owner_name' => 'test_owner_name',
        ]);
        $response->assertRedirect('/islands/1/plans');
        $this->assertNotNull(Island::find(1));
        $this->assertNotNull(IslandTerrain::find(1));
        $this->assertNotNull(IslandStatus::find(1));
        $this->assertNotNull(IslandPlan::find(1));
    }
}
