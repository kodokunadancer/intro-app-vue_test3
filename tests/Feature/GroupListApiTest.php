<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Group;
use App\Photo;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GroupListApiTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
    }

    /**
     * @test
     * 正常系テスト
     */
    public function should_正しい構造のJSONの返却(): void
    {
        factory(Group::class, 5)
            ->create()
            ->each(function ($group): void {
                $group->users()->save($this->user);
                $group->photo()->save(factory(Photo::class)->make());
            });

        $response = $this->actingAs($this->user)
            ->json('GET', route('index.group', [
                      'user' => $this->user->id,
                    ]));

        $groups = $this->user->groupUser()->with('photo')->get();

        //期待する返還されるJSONレスポンスを作成
        $expended_data = $groups->map(function ($group) {
            return [
          'id' => $group->id,
          'name' => $group->name,
          'password' => $group->password,
          'author_id' => $group->author_id,
          'photo' => $group->photo()->first(),
        ];
        })
            ->all();

        //以下から検証
        $response
            ->dump()
            ->assertStatus(200)
            ->assertJsonCount(5)
            ->assertExactJson($expended_data);
    }
}
