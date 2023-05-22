<?php

namespace Tests\App\Http\Controllers\Api\Islands;

use App\Models\Island;
use App\Models\IslandComment;
use App\Models\User;
use Tests\TestCase;

class CommentsControllerTest extends TestCase
{
    public function testPostSuccess()
    {
        $user = User::factory()->create();
        $island = Island::factory()->setUser($user)->create();

        $response = $this->actingAs($user)
            ->post('/api/islands/' . $island->id . '/comments', [
                'comment' => 'test_comment',
            ]);

        $response->assertOk();
        $this->assertSame(1, IslandComment::where('island_id', $island->id)->count());
        $islandComment = IslandComment::where('island_id', $island->id)->first();
        $this->assertSame($island->id, $islandComment->island_id);
        $this->assertSame('test_comment', $islandComment->comment);
        $this->assertSame('test_comment', json_decode($response->content())->comment);

        $response = $this->actingAs($user)
            ->post('/api/islands/' . $island->id . '/comments', [
                'comment' => '',
            ]);

        $response->assertOk();
        $this->assertSame(1, IslandComment::where('island_id', $island->id)->count());
        $this->assertNull(IslandComment::find($islandComment->id));
        $islandComment = IslandComment::where('island_id', $island->id)->first();
        $this->assertSame($island->id, $islandComment->island_id);
        $this->assertNull($islandComment->comment);
        $this->assertNull(json_decode($response->content())->comment);
    }

    public function testPostMaxChars()
    {
        $user = User::factory()->create();
        $island = Island::factory()->setUser($user)->create();

        $response = $this->actingAs($user)
            ->post('/api/islands/' . $island->id . '/comments', [
                'comment' => str_repeat('あ', 129),
            ]);

        $response->assertStatus(400);
        $this->assertNull(IslandComment::find(1));

        $response = $this->actingAs($user)
            ->post('/api/islands/' . $island->id . '/comments', [
                'comment' => str_repeat('あ', 128),
            ]);

        $response->assertOk();
    }

    public function testPostNotRegistered()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->post('/api/islands/' . 1 . '/comments', [
                'comment' => 'test_comment',
            ]);

        $response->assertForbidden();
        $this->assertNull(IslandComment::find(1));
    }

    public function testPostTryOthersIsland()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $island = Island::factory()->setUser($user2)->create();

        $response = $this->actingAs($user1)
            ->post('/api/islands/' . $island->id . '/comments', [
                'comment' => 'test_comment',
            ]);

        $response->assertForbidden();
        $this->assertNull(IslandComment::find(1));
    }

}
