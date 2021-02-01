<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\User;
use Tests\TestCase;

class CreateProfileApiTest extends TestCase
{
    public function setUp(): vid
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
    }

    /**
     * @test
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
                    'owner' => [
                      'name' => $this->user->name,
                    ],
                    'introduction' => 'テストと申します！',
                ]);

        //登録したプロフィールデータを取得
        $profiles = $this->user->profiles()->get();

        $this->assertEquals(1, $profiles->count());
        $this->assertEquals('テスト', $profiles[0]->name);
    }
}
