<?php

namespace Tests\App\Http\Controllers\Api\Islands;

use App\Models\Island;
use App\Models\IslandComment;
use App\Models\IslandHistory;
use App\Models\IslandStatus;
use App\Models\Turn;
use App\Models\User;
use Tests\TestCase;

class DetailControllerTest extends TestCase
{
    public function testPatchSuccess()
    {
        $user = User::factory()->create();
        $turn = Turn::latest()->first();
        $island = Island::factory()->setUser($user)->create();
        $islandStatus = IslandStatus::factory()->setTurn($turn)->setIsland($island)->setFunds(10000)->create();

        $response = $this->actingAs($user)
            ->patch('/api/islands/' . $island->id, [
                'name' => 'test_name',
            ]);

        $response->assertOk();
        $this->assertSame('test_name', Island::find(1)->name);
        $this->assertSame('test_name', json_decode($response->content())->island->name);
        $this->assertSame(IslandStatus::find(1)->funds, 9000);
        $this->assertSame(IslandHistory::find(1)->name, $island->name);

        $response = $this->actingAs($user)
            ->patch('/api/islands/' . $island->id, [
                'name' => 'test_name2',
                'owner_name' => 'test_owner_name2',
            ]);

        $response->assertOk();
        $this->assertSame('test_name2', Island::find(1)->name);
        $this->assertSame('test_owner_name2', Island::find(1)->owner_name);
        $this->assertSame('test_name2', json_decode($response->content())->island->name);
        $this->assertSame('test_owner_name2', json_decode($response->content())->island->owner_name);
        $this->assertSame(IslandStatus::find(1)->funds, 8000);
        $this->assertSame(IslandHistory::find(2)->name, 'test_name');
        $this->assertSame(IslandHistory::find(2)->owner_name, $island->owner_name);
    }
    public function testPatchNoParamsSent()
    {
        $user = User::factory()->create();
        $turn = Turn::latest()->first();
        $island = Island::factory()->setUser($user)->create();
        $islandStatus = IslandStatus::factory()->setTurn($turn)->setIsland($island)->setFunds(10000)->create();

        $response = $this->actingAs($user)
            ->patch('/api/islands/' . $island->id, [
            ]);

        $response->assertBadRequest();
        $this->assertSame(IslandStatus::find(1)->funds, 10000);
    }

    public function testPatchNotModify()
    {
        $user = User::factory()->create();
        $turn = Turn::latest()->first();
        $island = Island::factory()->setUser($user)->create();
        $islandStatus = IslandStatus::factory()->setTurn($turn)->setIsland($island)->setFunds(10000)->create();

        $response = $this->actingAs($user)
            ->patch('/api/islands/' . $island->id, [
                'name' => $island->name,
                'owner_name' => $island->owner_name,
            ]);

        $response->assertBadRequest();
        $this->assertSame(IslandStatus::find(1)->funds, 10000);
    }
    public function testPatchLackOfFunds()
    {
        $user = User::factory()->create();
        $turn = Turn::latest()->first();
        $island = Island::factory()->setUser($user)->create();
        $islandStatus = IslandStatus::factory()->setTurn($turn)->setIsland($island)->setFunds(500)->create();

        $response = $this->actingAs($user)
            ->patch('/api/islands/' . $island->id, [
                'name' => 'test_name',
            ]);

        $response->assertBadRequest();
        $this->assertSame(IslandStatus::find(1)->funds, 500);
    }
}
