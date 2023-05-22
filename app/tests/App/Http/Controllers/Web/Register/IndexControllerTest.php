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

        $this->assertSame(1, Island::where('user_id', $user->id)->count());
        $island = Island::where('user_id', $user->id)->first();
        $response->assertRedirect('/islands/' . $island->id . '/plans');
        $this->assertSame(1, IslandTerrain::where('island_id', $island->id)->count());
        $this->assertSame(1, IslandStatus::where('island_id', $island->id)->count());
        $this->assertSame(1, IslandPlan::where('island_id', $island->id)->count());
    }
}
