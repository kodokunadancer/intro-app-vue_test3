<?php

declare(strict_types=1);

namespace Tests\Unit\Model;

use App\Group;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GroupTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function should_Groupインスタンスに値を代入し保存できる(): void
    {
        $user = factory(User::class)->create();
        $group = new Group();
        $group->name = 'テスト';
        $user->group()->save($group);
        $user->groupUser()->save($group);

        $this->assertEquals('テスト', $group->name);
        $this->assertRegExp('/^[0-9a-zA-Z-_]{6}$/', $group->password);
        $this->assertEquals(1, $user->groupUser()->count());
    }
}
