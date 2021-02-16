<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginApiTest extends TestCase
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
    public function should_登録済みのユーザーを認証して返却する(): void
    {
        $response = $this
            ->json('POST', route('login'), [
                      'email' => $this->user->email,
                      'password' => 'password',
                    ]);

        $response
            ->assertStatus(200)
            ->assertJson(['name' => $this->user->name]);

        $this->assertAuthenticatedAs($this->user);
    }

    /**
     * @test
     * 異常系テスト
     */
    public function should_不一致の場合エラーテキストを返す(): void
    {
        $test_user = factory(User::class)->make();
        $response = $this
            ->json('POST', route('login'), [
                     'email' => $test_user->email,
                     'password' => $test_user->password,
           ]);

        $response
            ->assertStatus(422)
            ->assertJsonFragment([
                  'errors' => [
                      'email' => ['ログイン情報が登録されていません。'],
                    ],
              ]);
    }
}
