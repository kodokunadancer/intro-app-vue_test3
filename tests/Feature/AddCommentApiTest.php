<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Group;
use App\Profile;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AddCommentApiTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        factory(User::class, 2)->create();

        //コメントする側とされる側のユーザーを取得
        $this->active_user = User::where('id', 1)->first();
        $this->passive_user = User::where('id', 2)->first();

        $this->group = $this->active_user->groupUser()->save(factory(Group::class)->make());

        //コメントする側とされる側のプロフィールを取得
        $this->active_profile = factory(Profile::class)->create(['user_id' => $this->active_user->id]);
        $this->passive_profile = factory(Profile::class)->create(['user_id' => $this->passive_user->id]);
    }

    /**
     * @test
     * 正常系テスト
     */
    public function should_コメントを追加できる(): void
    {
        $content = 'sample content';

        $response = $this->actingAs($this->active_user)
            ->json('POST', route('add.comment', [
                'user' => $this->active_user->id,
                'group' => $this->group->id,
                'profile' => $this->passive_profile->id,
            ]), compact('content'));

        $comments = $this->passive_profile->comments()->get();
        $response->assertStatus(201)
            ->assertJson([[
                  'author' => [
                      'name' => $this->active_profile->name,
                  ],
                  'content' => $content,
            ]]);

        $this->assertEquals(1, $comments->count());
        $this->assertEquals($content, $comments[0]->content);
        $this->assertEquals($content, $response[0]['content']);
    }

    /**
     * @test
     * 異常系テスト
     */
    public function should_空のコメントを投稿した場合エラーテキストを返す(): void
    {
        $response = $this->actingAs($this->active_user)
            ->json('POST', route('add.comment', [
                'user' => $this->active_user->id,
                'group' => $this->group->id,
                'profile' => $this->passive_profile->id,
          ]));

        $response->assertStatus(422)
            ->assertJsonFragment([
                    'errors' => [
                        'content' => ['コンテンツは必ず指定してください。'],
                     ],
              ]);
    }

    /**
     * @test
     * 異常系テスト
     */
    public function should_文字数オーバーの場合エラーテキストを返す(): void
    {
        $content = str_repeat('a', 501);
        $response = $this->actingAs($this->active_user)
            ->json('POST', route('add.comment', [
                 'user' => $this->active_user->id,
                 'group' => $this->group->id,
                 'profile' => $this->passive_profile->id,
             ]), compact('content'));

        $response->assertStatus(422)
            ->assertJsonFragment([
                  'errors' => [
                      'content' => ['コンテンツは、500文字以下で指定してください。'],
                  ],
               ]);
    }
}
