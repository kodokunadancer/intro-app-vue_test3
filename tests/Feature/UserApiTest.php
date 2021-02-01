<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserApiTest extends TestCase
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
    public function should_ログイン中のユーザーを返却する()
    {
        $response = $this->actingAs($this->user)->json('GET', route('get.user'));

        $response
            ->assertStatus(200)
            ->assertJson([
                'name' => $this->user->name,
            ]);
    }

    /**
     * @test
     */

    //ログインしていない場合、この場合通常プログラムはnullを返すが、webAPIの場合HTTPレスポンスに変換される際に空文字に変わって値が返る
    //webAPIの場合、レスポンスはHTTPレスポンスであり、HTTPレスポンスはただの文字列であるため、nullやfalseという概念は存在しない
    public function should_ログインされていない場合は空文字を返却する()
    {
        //ログインしていない状態でログインユーザー取得処理を実行してみる
        $response = $this->json('GET', route('get.user'));

        //以下から検証（テスト対象）
        $response->assertStatus(200);
        //非ログイン状態の場合、予定通り空文字が返って来ているか確認
        $this->assertEquals("", $response->content());
    }
}
