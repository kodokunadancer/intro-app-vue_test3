<?php

namespace Tests\Feature;

use App\User;
use App\Group;
use App\Profile
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LikeApiTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        //ダミーユーザーデータを取得
        $this->user = factory(User::class)
                    ->create()
                    ->groups()->save(factory(Group::class)->create());
        $this->group = Group::first();
        $this->comment = factory(Comment::class)->create();
        $this->profile = factory(Profile::class)->create();
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
                'profile' => $this->profile->id,
                'comment' => $this->comment->id,
            ]));

        $response->assertStatus(200)
            ->assertJsonFragment([
                'comment_id' => $this->comment->id,
            ]);

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
          'profile' => $this->profile->id,
          'comment' => $this->comment->id,
        ];

        $this->actingAs($this->user)->json('PUT', route('photo.like', $param));
        $this->actingAs($this->user)->json('PUT', route('photo.like', $param));

        $this->assertEquals(1, $this->photo->likes()->count());
    }

    /**
     * @test
     */
    public function should_いいねを解除できる()
    {
        //あらかじめいいねしてある状態にしておく
        $this->comment->likes()->attach($this->profile->id);

        //非同期でいいね削除処理をさせる
        $response = $this->actingAs($this->user)
            ->json('DELETE', route('like.comment', [
              'user' => $this->user->id,
              'group' => $this->group->id,
              'profile' => $this->profile->id,
              'comment' => $this->comment->id,
            ]));

        //JSONレスポンスに期待するデータが存在するか
        $response->assertStatus(200)
            ->assertJsonFragment([
                'comment_id' => $this->comment->id,
            ]);

        //DBにダミー写真にいいねが付与されていないか
        //つまり写真データに紐づくいいね数が 0 であるかどうか
        $this->assertEquals(0, $this->comment->likes()->count());
    }
}
