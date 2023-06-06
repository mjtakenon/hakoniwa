<?php

namespace Tests\App\Http\Controllers\Api\Islands\Bbs;

use App\Models\Island;
use App\Models\IslandBbs;
use App\Models\IslandComment;
use App\Models\IslandStatus;
use App\Models\Turn;
use App\Models\User;
use Tests\TestCase;

class IndexControllerTest extends TestCase
{
    public function testPostSuccess()
    {
        $turn = Turn::latest()->first();
        $user = User::factory()->create();
        $island = Island::factory()->setUser($user)->create();
        IslandStatus::factory()->setTurn($turn)->setIsland($island)->setFunds(10000)->create();

        $response = $this->actingAs($user)
            ->post('/api/islands/' . $island->id . '/bbs', [
                'comment' => 'test_comment',
                'visibility' => 'public',
            ]);

        $response->assertOk();
        $this->assertSame(1, IslandBbs::where('island_id', $island->id)->count());
        $islandBbs = IslandBbs::where('island_id', $island->id)->first();
        $this->assertSame('test_comment', $islandBbs->comment);
        $this->assertSame('public', $islandBbs->visibility);


        $response = $this->actingAs($user)
            ->post('/api/islands/' . $island->id . '/bbs', [
                'comment' => 'private_comment',
                'visibility' => 'private',
            ]);


        $user2 = User::factory()->create();
        $island2 = Island::factory()->setUser($user2)->create();
        IslandStatus::factory()->setTurn($turn)->setIsland($island2)->setFunds(10000)->create();
        $response = $this->actingAs($user2)
            ->post('/api/islands/' . $island->id . '/bbs', [
                'comment' => 'hoge',
                'visibility' => 'private',
            ]);

        $response = $this->actingAs($user)
            ->post('/api/islands/' . $island->id . '/bbs', [
                'comment' => 'test_comment',
                'visibility' => 'public',
            ]);

        $user3 = User::factory()->create();
        $island3 = Island::factory()->setUser($user3)->create();
        IslandStatus::factory()->setTurn($turn)->setIsland($island3)->setFunds(10000)->create();
        $response = $this->actingAs($user3)
            ->post('/api/islands/' . $island->id . '/bbs', [
                'comment' => 'hoge',
                'visibility' => 'private',
            ]);

        dd($response->content());
    }

}
