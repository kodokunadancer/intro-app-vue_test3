<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Photo;
use App\Profile;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EditProfileApiTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
        $this->profile = factory(Profile::class)->create(['user_id' => $this->user->id]);
        $this->data = [
            'photo' => UploadedFile::fake()->image('photo.jpg'),
            'textName' => 'テストネーム',
            'textIntroduction' => 'テストイントロダクション',
        ];
    }

    /**
     * @test
     * 正常系テスト
     */
    public function should_プロフィールを編集できる(): void
    {
        // S3ではなくテスト用のストレージを使用する
        // → storage/framework/testing
        Storage::fake('s3');

        $response = $this->actingAs($this->user)
            ->json(
                'POST',
                route('edit.myProfile', [
                'user' => $this->user->id,
            ]),
                $this->data,
            );

        $response->assertStatus(201);

        $profile = Profile::get();
        $photo = Photo::first();
        $this->assertEquals(1, $profile->count());
        $this->assertEquals('テストネーム', $profile[0]->name);
        $this->assertEquals('テストイントロダクション', $profile[0]->introduction);
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
        // 乱暴だがこれでDBエラーを起こす
        // データベースごと破損させる
        Schema::drop('photos');

        Storage::fake('s3');

        $response = $this->actingAs($this->user)
            ->json(
                'POST',
                route('edit.myProfile', [
                'user' => $this->user->id,
            ]),
                $this->data,
            );

        $response->assertStatus(500);
        //ストレージにファイルが保存されていないこと
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
                route('edit.myProfile', [
                'user' => $this->user->id,
            ]),
                $this->data,
            );

        $response->assertStatus(500);
        $this->assertEmpty(Photo::all());
    }
}
