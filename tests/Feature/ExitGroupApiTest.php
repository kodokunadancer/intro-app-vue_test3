<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Group;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExitGroupApiTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
        $this->group = $this->user->groupUser()->save(factory(Group::class)->make());
    }

    /**
     * @test
     */
    public function should_グループを退会できる(): void
    {
        $response = $this->actingAs($this->user)
            ->json('GET', route('exit.group', [
              'user' => $this->user->id,
              'group' => $this->group->id,
          ]));

        $response->assertStatus(200);
        $this->assertEquals(0, $this->user->groupUser()->count());
    }
}
