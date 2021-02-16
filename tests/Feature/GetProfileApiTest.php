<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Profile;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetProfileApiTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
        $this->profile = factory(Profile::class)->create(['user_id' => $this->user->id]);
    }

    /**
     * @test
     */
    public function should_ログイン中のユーザーのプロフィールを取得できる(): void
    {
        $response = $this->actingAs($this->user)->json('GET', route('get.profile'));
        $response->assertStatus(200)
            ->assertJson([
              'owner' => [
                    'name' => $this->user->name,
              ],
              'introduction' => $this->profile->introduction,
          ]);
    }

    public function should_ログインしていない場合は空文字列を返す(): void
    {
        $response = $this->json('GET', route('get.profile'));
        $response->assertStatus(200);
        $this->assertEquals('', $response->content());
    }
}
