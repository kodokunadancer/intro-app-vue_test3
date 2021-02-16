<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Group;
use App\Photo;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EditGroupApiTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
        $this->group = $this->user->groupUser()->save(factory(Group::class)->make());
        $this->data = [
        'photo' => UploadedFile::fake()->image('photo.jpg'),
        'name' => 'テストネーム',
      ];
    }

    /**
     * @test
     * 正常系テスト
     */
    public function should_グループを編集できる(): void
    {
        Storage::fake('s3');
        $response = $this->actingAs($this->user)
            ->json(
                'POST',
                route('edit.group', [
                'user' => $this->user->id,
                'group' => $this->group->id,
          ]),
                $this->data
            );

        $group = Group::get();
        $photo = Photo::first();

        $response->assertStatus(201);
        $this->assertEquals(1, $group->count());
        $this->assertEquals('テストネーム', $group[0]->name);
        // 写真のIDが12桁のランダムな文字列であること
        $this->assertRegExp('/^[0-9a-zA-Z-_]{12}$/', $photo->random_id);
        Storage::cloud()->assertExists($photo->filename);
    }

    /**
     * @test
     * 異常系テスト
     */
    public function should_データベースエラーの場合はファイルを保存しない(): void
    {
        Schema::drop('photos');

        Storage::fake('s3');

        $response = $this->actingAs($this->user)
            ->json(
                'POST',
                route('edit.group', [
                'user' => $this->user->id,
                'group' => $this->group->id,
          ]),
                $this->data
            );

        $response->assertStatus(500);
        $this->assertCount(0, Storage::cloud()->files());
    }

    /**
     * @test
     * 異常系テスト
     */
    public function should_ファイル保存エラーの場合はDBへの挿入はしない(): void
    {
        // ストレージをモックして保存時にエラーを起こさせる
        Storage::shouldReceive('cloud')
            ->once()
            ->andReturnNull();

        $response = $this->actingAs($this->user)
            ->json(
                'POST',
                route('edit.group', [
                'user' => $this->user->id,
                'group' => $this->group->id,
            ]),
                $this->data
            );

        $response->assertStatus(500);
        $this->assertEmpty(Photo::all());
    }
}
