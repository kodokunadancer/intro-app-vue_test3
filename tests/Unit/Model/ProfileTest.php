<?php

declare(strict_types=1);

namespace Tests\Unit\Model;

use App\Profile;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function should_Profileインスタンスに値を代入し保存できる(): void
    {
        $user = factory(User::class)->create();
        $profile = new Profile();
        $profile->name = 'テスト';
        $profile->introduction = 'テスト自己紹介';
        $user->profiles()->save($profile);

        $this->assertEquals('テスト', $profile->name);
        $this->assertEquals('テスト自己紹介', $profile->introduction);
    }
}
