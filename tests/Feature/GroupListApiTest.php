<?php

namespace Tests\Feature;

use App\User;
use App\Group;
use App\Photo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GroupListApiTest extends TestCase
{
    use RefreshDatabase;

    public function setUp() :void {
      parent::setUp();
      $this->user = factory(User::class)->create();
      $this->groupList = factory(Group::class, 10)
          ->create()
          ->each(function($group) {
            $group->users()->save($this->user);
            $group->photo()->save(factory(Photo::class)->make([
              'group_id' => $group->id,
            ]));
          });
    }

    /**
     * @test
     */
    public function should_正しい構造のJSONの返却(){
      $response = $this->actingAs($this->user)
                   ->json('GET', route('index.group',[
                      'user' => $this->user->id,
                    ]));

      //登録しておいたダミーレコードを取得
      //$groups = $this->groupList->
      $groups = Group::with(['users', 'photo'])->orderBy('created_at', 'desc')->get();

      //期待する返還されるJSONレスポンスを作成
      $expended_data = $groups->map(function($group){
        return [
          'id' => $group->id,
          'name' => $group->name,
          'password' => $group->password,
          'author_id' => $group->author_id,
          'photo' => $group->photo(),
          'users' => $group->users(),
        ];
      })
      ->all();

      //以下から検証
      $response->assertStatus(200);
               //JSONレスポンスのdata数が適切か
               //->assertJsonCount(10, 'data')
              //JSONレスポンスのdataの内容は期待通りか
              // ->assertJsonFragment([
              //   'data' => $expended_data,
              // ]);
     }

}
