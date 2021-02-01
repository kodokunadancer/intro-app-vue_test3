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

        // ダミーユーザーを作成する。その時ダミーグループを作成し（同時に保存もしておく）そのグループ一と多対多のリレーションになる設定をする。
        // 多対多のリレーション関係にしておかなければミドルウェアにひっかかる
        $this->user = factory(User::class)
            ->create()
            ->groups()->save(factory(Group::class)->create());
        $this->group = Group::first();
        $this->profile = factory(Profile::class)->create();
    }

    /**
     * @test
     */
    public function should_コメントを追加できる(): void
    {
        //テスト写真データ作成
        factory(Photo::class)->create();
        //生成したテストデータ取得
        $photo = Photo::first();

        //コメント投稿リクエストする際のコメント内容
        $content = 'sample content';

        $response = $this->actingAs($this->user)
            ->json('POST', route('add.comment', [
                'user' => $this->user->id,
                'group' => $this->group->id,
                'profile' => $this->profile->id,
            ]), compact('content'));

        //ダミー写真データに紐づくコメントデータを取得
        $comments = $profile->comments()->get();

        $response->assertStatus(201)
            // JSONフォーマットが期待通りであること
            ->assertJsonFragment([
                'author' => [
                    'name' => $this->user->name,
                ],
                'content' => $content,
            ]);

        // DBにコメントが1件登録されていること
        $this->assertEquals(1, $comments->count());
        // 内容がAPIでリクエストしたものであること
        $this->assertEquals($content, $comments[0]->content);
    }
}
