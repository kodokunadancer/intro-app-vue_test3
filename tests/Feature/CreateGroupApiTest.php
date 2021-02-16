<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Group;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateGroupApiTest extends TestCase
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
    public function should_グループ作成(): void
    {
        $data = [
            'name' => 'グループ名',
        ];
        $response = $this->actingAs($this->user)
            ->json(
                'POST',
                route('create.group', [
                  'user' => $this->user->id,
            ]),
                $data
            );

        $group = Group::get();

        $response
            ->assertStatus(201)
            ->assertJsonFragment([
                'name' => 'グループ名',
            ]);

        $this->assertEquals(1, $group->count());
        $this->assertEquals('グループ名', $group[0]->name);
    }

    /**
     * @test
     * 異常系テスト
     */
    public function should_何も入力せずに送信した場合該当のエラーテキストを返す()
    {
        $response = $this->actingAs($this->user)
            ->json(
                'POST',
                route('create.group', [
                  'user' => $this->user->id,
            ]));

        $response
            ->assertStatus(422)
            ->assertJsonFragment([
                'errors' => [
                    'name' => ['グループ名は必ず指定してください。']
                ],
            ]);
    }

    /**
     * @test
     * 異常系テスト
     */
    public function should_文字数オーバーした場合該当のエラーテキストを返す()
    {
        $data = [
            'name' => str_repeat('a', 21),
        ];
        $response = $this->actingAs($this->user)
            ->json(
                'POST',
                route('create.group', [
                  'user' => $this->user->id,
            ]), $data
          );

        $response
            ->assertStatus(422)
            ->assertJsonFragment([
                'errors' => [
                    'name' => ['グループ名は、20文字以下で指定してください。']
                ],
            ]);
    }
}
