<?php

namespace Tests\App\Http\Controllers\Register;

use App\Models\User;
use Tests\TestCase;

class IndexControllerTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
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
