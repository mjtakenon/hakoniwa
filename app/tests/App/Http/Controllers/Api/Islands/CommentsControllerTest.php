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
        $this->assertNotNull(IslandComment::find(1));
        $this->assertSame('test_comment', IslandComment::find(1)->comment);

        $response = $this->actingAs($user)
            ->post('/api/islands/' . $island->id . '/comments', [
                'comment' => 'test_comment2',
            ]);

        $response->assertOk();
        $this->assertNull(IslandComment::find(1));
        $this->assertSame('test_comment2', IslandComment::find(2)->comment);
        $this->assertSame('test_comment2', json_decode($response->content())->comment);
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

        $response->assertStatus(403);
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

        $response->assertStatus(403);
        $this->assertNull(IslandComment::find(1));
    }

}
