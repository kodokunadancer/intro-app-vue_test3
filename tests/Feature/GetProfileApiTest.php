<?php

namespace Tests\Feature;

use App\User;
use App\Profile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GetProfileApiTest extends TestCase
{
    use RefreshDatabase;

    public function setUp() :void {
      parent::setUp();
      $this->user = factory(User::class)->create();
    }

    /**
     * @test
     */
    public function should_ログイン中のユーザーのプロフィールを取得できる() {
      factory(Profile::class)->create(['user_id' => $this->user->id]);
      $response = $this->actingAs($this->user)->json('GET', route('get.profile'));
      $response->assertStatus(200);
               // ->assertJsonFragment([
               //   'owner' => [
               //     'name' => $this->user->name,
               //   ],
               //   'introduction' => $this->profile->name,
               // ]);
    }

    public function should_ログインしていない場合は空文字列を返す() {
      $response = $this->json('GET', route('get.profile'));
      $response->assertStatus(200);
      $this->assertEquals('', $response->content());
    }
}
