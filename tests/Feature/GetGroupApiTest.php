<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Group;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetGroupApiTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->group = factory(Group::class)->create();
    }

    /**
     * @test
     */
    public function should_グループを取得できる(): void
    {
        $response = $this->json('GET', route('get.group', [
          'group' => $this->group->id,
      ]));
        $response->assertStatus(200)
            ->assertJsonFragment([
            'name' => $this->group->name,
         ]);
    }
}
