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

        //テストユーザー作成
        //ここで作成したダミーデータを認証対象のデータにする、つまりあらかじめデータを作成しておく
        $this->user = factory(User::class)->create();
    }

    /**
     * @test
     */
    public function should_登録済みのユーザーを認証して返却する(): void
    {
        //非同期でログイン処理するリクエストを送り、そのレスポンスを受け取る
        //認証するユーザーデータは上記で作成したダミーデータを使用する
        $response = $this
            ->json('POST', route('login'), [
                      'email' => $this->user->email,
                      'password' => 'password',
                    ]);

        //以下が検証（テスト対象）

        //レスポンスが正常値か
        //JSONレスポンスに認証ユーザー情報が入っているか
        $response
            ->assertStatus(200)
            ->assertJson(['name' => $this->user->name]);

        //上記で作成したダミーレコードが認証されているか
        //つまり上記のリクエストが正常に稼働しログイン処理できているか
        $this->assertAuthenticatedAs($this->user);
    }
}
