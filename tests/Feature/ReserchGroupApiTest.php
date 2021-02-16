<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Group;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReserchGroupApiTest extends TestCase
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
    public function should_グループを検索できる(): void
    {
        $data = [
         'group_name' => 'グループ名',
         'password' => $this->group->password,
       ];
        $response = $this->actingAs($this->user)
            ->json('POST', route('reserch.group', [
                  'user' => $this->user->id,
            ]), $data);

        $group = Group::first();

        $response->assertStatus(200)
            ->assertJsonFragment([
                'name' => 'グループ名',
                'password' => $this->group->password,
            ]);
    }

    /**
     * @test
     * 異常系テスト
     */
    public function should_検索したグループが存在しない場合は該当のステータスコードとエラーテキストを返す(): void
    {
         $data = [
          'group_name' => '存在しないグループ名',
          'password' => $this->group->password,
        ];
         $response = $this->actingAs($this->user)
             ->json('POST', route('reserch.group', [
                   'user' => $this->user->id,
             ]), $data);

         $response
             ->assertStatus(400)
             ->assertJsonFragment([
                'error' => 'NotGroup'
             ]);
     }

     /**
      * @test
      * 異常系テスト
      */
     public function should_検索したグループにすでに参加済みの場合は該当のステータスコードとエラーテキストを返す(): void
     {
          $this->user->groups()->save($this->group);
          $data = [
           'group_name' => 'グループ名',
           'password' => $this->group->password,
         ];
          $response = $this->actingAs($this->user)
              ->json('POST', route('reserch.group', [
                    'user' => $this->user->id,
              ]), $data);

          $response->assertStatus(400)
              ->assertJsonFragment([
                'error' => 'Participated'
              ]);
      }
}
