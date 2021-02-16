<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Group;
use App\Profile;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ForceUserApiTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
        $this->group = $this->user->groupUser()->save(factory(Group::class)->make(['author_id' => $this->user->id]));
        $this->exit_user = factory(User::class)->create();
        $this->group->users()->save($this->exit_user);
        $this->profile = $this->exit_user->profiles()->save(factory(Profile::class)->make());
    }

    /**
     * @test
     * 正常系テスト
     */
    public function should_強制退会(): void
    {
        $response = $this->actingAs($this->user)
            ->json('GET', route('force.user', [
                'user' => $this->user->id,
                'group' => $this->group->id,
                'profile' => $this->profile->id,
          ]));

        $response->assertStatus(200);

        $this->assertEquals(1, $this->group->users()->count());
    }
}
