<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LogoutApiTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        // テストユーザー作成
        $this->user = factory(User::class)->create();
    }

    /**
     * @test
     */
    public function should_認証済みのユーザーをログアウトさせる()
    {
        //$this->actingAs(ユーザー)によって、引数のユーザーでログインしている状態を作れる
        //ダミーデータでログインしている状態を作り、非同期でログアウト処理をさせる
        $response = $this->actingAs($this->user)
                         ->json('POST', route('logout'));

        //以下が検証（テスト対象）
        //レスポンスは正常か
        $response->assertStatus(200);
        //ユーザーが認証されていないか
        $this->assertGuest();
    }
}
