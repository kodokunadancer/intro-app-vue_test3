<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Comment;
use App\Group;
use App\Profile;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LikeApiTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
        $this->active_profile = factory(Profile::class)->create([
          'user_id' => $this->user->id,
        ]);
        $this->passive_profile = factory(Profile::class)->create();
        $this->group = factory(Group::class)->create()->users()->save($this->user);
        $this->comment = factory(Comment::class)->create();
    }

    /**
     * @test
     * 正常系テスト
     */
    public function should_いいねを追加できる(): void
    {
        $response = $this->actingAs($this->user)
            ->json('PUT', route('like.comment', [
                'user' => $this->user->id,
                'group' => $this->group->id,
                'profile' => $this->passive_profile->id,
                'comment' => $this->comment->id,
            ]));

        $response->assertStatus(200)
            ->assertJsonFragment([
              'comment_id' => $this->comment->id,
          ]);

        $this->assertEquals(1, $this->comment->likes()->count());
    }

    /**
     * @test
     * 正常系テスト
     */
    public function should_いいねを解除できる(): void
    {
        //あらかじめいいねしてある状態にしておく
        $this->comment->likes()->attach($this->active_profile);

        $response = $this->actingAs($this->user)
            ->json('DELETE', route('like.comment', [
              'user' => $this->user->id,
              'group' => $this->group->id,
              'profile' => $this->passive_profile->id,
              'comment' => $this->comment->id,
            ]));

        $response->assertStatus(200)
            ->assertJsonFragment([
              'comment_id' => $this->comment->id,
          ]);

        $this->assertEquals(0, $this->comment->likes()->count());
    }

    /**
     * @test
     * 異常系テスト
     */
    public function should_2回同じ写真にいいねしても1個しかいいねがつかない(): void
    {
        $param = [
          'user' => $this->user->id,
          'group' => $this->group->id,
          'profile' => $this->passive_profile->id,
          'comment' => $this->comment->id,
        ];

        $this->actingAs($this->user)->json('PUT', route('like.comment', $param));
        $this->actingAs($this->user)->json('PUT', route('like.comment', $param));

        $this->assertEquals(1, $this->comment->likes()->count());
    }
}
