<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function should_新しいユーザーを作成して返却する(): void
    {
        //会員登録処理にかけるデータを定義しておく
        $data = [
            'name' => 'vuesplash user',
            'email' => 'dummy@email.com',
            'password' => 'test1234',
            'password_confirmation' => 'test1234',
        ];

        //定義したデータを会員登録処理にかけるリクエストを非同期で行い、そのリクエストを変数に代入
        $response = $this->json('POST', route('register'), $data);

        //上記で会員登録したユーザーデータを取得
        $user = User::first();

        //以下が結果の検証（テスト事項）

        //上記で定義したデータとデータベースに登録したデータが等しいか確認
        //つまりデータベースにきちんと指定のデータが登録できているか確認
        $this->assertEquals($data['name'], $user->name);

        //レスポンスが正常値か
        //レスポンスのJSONデータに登録したいデータが含まれているか
        $response
            ->assertStatus(201)
            ->assertJson(['name' => $user->name]);
    }
}
