<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Group;
use App\Profile;
use App\User;
use App\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Http\JsonResponse;

class AddCommentApiTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        //ユーザーとそれに紐づくプロフィールを２件ずつ作成
        factory(User::class, 2)
            ->create();
            // ->each(function ($user){
            //     $user->profiles()->save(factory(Profile::class)->make());
            // });
    }

    /**
     * @test
     */
    public function should_コメントを追加できる()
    {
        //コメントする側とされる側のユーザーを取得
        $active_user = User::where('id', 1)->first();
        $passive_user = User::where('id', 2)->first();

        $active_user->groups()->save(factory(Group::class)->make());
        $group = Group::first();

        factory(Profile::class)->create(['user_id' => $active_user->id]);
        factory(Profile::class)->create(['user_id' => $passive_user->id]);

        $active_profile = Profile::where('user_id', $active_user->id)->first();
        $passive_profile = Profile::where('user_id', $passive_user->id)->first();

        $content = 'sample content';

        $response = $this->actingAs($active_user)
            ->json('POST', route('add.comment', [
                'user' => $active_user->id,
                'group' => $group->id,
                'profile' => $passive_profile->id,
            ]), compact('content'));

        $comments = $passive_profile->comments()->get();

        $response->assertStatus(200)
            ->assertJsonFragment([
                "author" => [
                    "name" => $active_profile->name,
                ],
                "content" => $content,
            ]);


        // DBにコメントが1件登録されていること
        $this->assertEquals(1, $comments->count());
        // 内容がAPIでリクエストしたものであること
        $this->assertEquals($content, $comments[0]->content);
        $this->assertEquals($content, $response[0]['content']);
    }
}
