<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateProfileApiTest extends TestCase
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
    public function should_プロフィール作成(): void
    {
        $data = [
         'name' => 'テスト',
         'introduction' => 'テストと申します！',
       ];
        $response = $this->actingAs($this->user)->json('POST', route('create.profile'), $data);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'name' => $this->user->name,
            ]);

        $profiles = $this->user->profiles()->get();

        $this->assertEquals(1, $profiles->count());
        $this->assertEquals('テスト', $profiles[0]->name);
    }

    /**
     * @test
     * 異常系テスト
     */
    public function should_何も入力せずに送信した場合エラーテキストを返す(): void
    {
        $response = $this->actingAs($this->user)->json('POST', route('create.profile'));
        $response->assertStatus(422)
            ->assertJsonFragment([
                 'errors' => [
                     'name' => ['名前は必ず指定してください。'],
                     'introduction' => ['自己紹介は必ず指定してください。'],
                  ],
             ]);
    }

    /**
     * @test
     * 異常系テスト
     */
    public function should_文字数オーバーした場合エラーテキストを返す(): void
    {
        $data = [
            'name' => str_repeat('a', 21),
            'introduction' => str_repeat('a', 501),
          ];

        $response = $this->actingAs($this->user)->json('POST', route('create.profile'), $data);
        $response->assertStatus(422)
            ->assertJsonFragment([
                  'errors' => [
                      'name' => ['名前は、20文字以下で指定してください。'],
                      'introduction' => ['自己紹介は、500文字以下で指定してください。'],
                   ],
              ]);
    }
}
