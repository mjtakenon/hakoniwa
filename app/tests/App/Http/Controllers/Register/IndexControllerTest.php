<?php

namespace Tests\App\Http\Controllers\Register;

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
        $response->assertOk();
    }
}
