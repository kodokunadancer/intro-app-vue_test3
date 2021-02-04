<?php

namespace Tests\Feature;

use App\User;
use App\Group;
use App\Profile;
use App\Comment;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

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
     */
    public function should_いいねを追加できる()
    {
        $response = $this->actingAs($this->user)
            ->json('PUT', route('like.comment', [
                'user' => $this->user->id,
                'group' => $this->group->id,
                'profile' => $this->passive_profile->id,
                'comment' => $this->comment->id,
            ]));

        $response->assertStatus(200);
            // ->assertJsonFragment([
            //     'comment_id' => $this->comment->id,
            // ]);

        //DBにいいねが登録されているか
        //つまり写真データに紐づくいいね数が１であるかどうか
        $this->assertEquals(1, $this->comment->likes()->count());
    }

    /**
     * @test
     */
    public function should_2回同じ写真にいいねしても1個しかいいねがつかない()
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

    /**
     * @test
     */
    public function should_いいねを解除できる()
    {
        //あらかじめいいねしてある状態にしておく
        $this->comment->likes()->attach($this->active_profile);

        //非同期でいいね削除処理をさせる
        $response = $this->actingAs($this->user)
            ->json('DELETE', route('like.comment', [
              'user' => $this->user->id,
              'group' => $this->group->id,
              'profile' => $this->passive_profile->id,
              'comment' => $this->comment->id,
            ]));

        //JSONレスポンスに期待するデータが存在するか
        $response->assertStatus(200);
            // ->assertJsonFragment([
            //     'comment_id' => $comment->id,
            // ]);

        //DBにダミー写真にいいねが付与されていないか
        //つまり写真データに紐づくいいね数が 0 であるかどうか
        $this->assertEquals(0, $this->comment->likes()->count());
    }
}
