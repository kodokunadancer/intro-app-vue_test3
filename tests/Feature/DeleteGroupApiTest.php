<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Group;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteGroupApiTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
        $this->group = $this->user->groupUser()->save(factory(Group::class)->make(['author_id' => $this->user->id]));
    }

    /**
     * @test
     * 正常系テスト
     */
    public function should_グループ削除(): void
    {
        $response = $this->actingAs($this->user)
            ->json('GET', route('delete.group', [
                'user' => $this->user->id,
                'group' => $this->group->id,
          ]));

        $group = Group::get();

        $response->assertStatus(200);

        $this->assertEquals(0, $this->group->count());
        $this->assertEquals(0, $this->user->groupUser()->count());
    }
}
