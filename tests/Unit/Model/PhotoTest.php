<?php

namespace Tests\Unit\Model;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Profile;
use App\Group;

class PhotoTest extends TestCase
{
  use RefreshDatabase;

  /**
   * @test
   */
  public function should_Photoインスタンスに値を代入し保存できる(): void
  {
      $profile = factory(Profile::class)->create();
      $group = factory(Group::class)->create();

      $photo = new Photo();
      $photo->profile_id = $profile->id,
      $photo->group_id = $group->id,
      $photo->filename = 'test.jpg'

      $this->assertEquals($profile->id, $photo->profile_id);
      $this->assertEquals($group->id, $photo->group_id);
      $this->assertEquals('filename', $photo->filename);
      $this->assertRegExp('/^[0-9a-zA-Z-_]{12}$/', $photo->random_id);
      $this->assertEquals(1, $photo->count());
  }
}
