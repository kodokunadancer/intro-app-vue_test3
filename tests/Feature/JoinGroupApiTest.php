<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Group;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JoinGroupApiTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
        $this->group = factory(Group::class)->create(['name' => 'グループ名']);
    }

    /**
     * @test
     * 正常系テスト
     */
    public function should_グループ参加(): void
    {
        $response = $this->actingAs($this->user)
            ->json('GET', route('join.group', [
                  'user' => $this->user->id,
                  'group' => $this->group->id,
            ]));

        $group = Group::first();

        $response
            ->assertStatus(201)
            ->assertJsonFragment([
                'name' => 'グループ名',
            ]);

        $this->assertEquals(1, $this->user->groupUser()->count());
    }
}
