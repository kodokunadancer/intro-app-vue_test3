<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Photo;
use App\User;
use App\Profile;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PhotoSubmitApiTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
        factory(Profile::class)->create([
          'user_id' => $this->user->id,
        ]);
    }

    /**
     * @test
     */
    public function should_ファイルをアップロードできる(): void
    {
        $data = [
          'photo' => UploadedFile::fake()->image('photo.jpg'),
          'textName' => 'テストネーム',
          'textIntroduction' => 'テストイントロダクション',
        ];
        // S3ではなくテスト用のストレージを使用する
        // → storage/framework/testing
        Storage::fake('s3');

        //非同期でファイルアップロード処理を実行させる
        $response = $this->actingAs($this->user)
            ->json('POST', route('edit.myProfile',[
                'user' => $this->user->id,
            ]), [
                // ダミーファイルを作成して送信している
                'photo' => UploadedFile::fake()->image('photo.img'),
                'textName' => 'テストネーム',
                'textIntroduction' => 'テストイントロダクション',
            ]);

        // レスポンスが201(CREATED)であること
        $response->assertStatus(200);

        $photo = Photo::first();

        // 写真のIDが12桁のランダムな文字列であること
        $this->assertRegExp('/^[0-9a-zA-Z-_]{12}$/', $photo->id);

        // DBに挿入されたファイル名のファイルがストレージに保存されていること
        Storage::cloud()->assertExists($photo->filename);
    }

    /**
     * @test
     */
    public function should_データベースエラーの場合はファイルを保存しない(): void
    {
        // 乱暴だがこれでDBエラーを起こす
        // データベースごと破損させる
        Schema::drop('photos');

        Storage::fake('s3');

        //非同期でファイルアップロード処理をさせる
        $response = $this->actingAs($this->user)
            ->json('POST', route('edit.myProfile',[
                'user' => $this->user->id,
            ]), [
                // ダミーファイルを作成して送信している
                'photo' => UploadedFile::fake()->image('photo.jpg'),
                'textName' => 'テストネーム',
                'textIntroduction' => 'テストイントロダクション',
            ]);

        // レスポンスが500(INTERNAL SERVER ERROR)であること
        $response->assertStatus(500);

        // ストレージにファイルが保存されていないこと
        $this->assertCount(0, Storage::cloud()->files());
    }

    /**
     * @test
     */
    public function should_ファイル保存エラーの場合はDBへの挿入はしない(): void
    {
        // ストレージをモックして保存時にエラーを起こさせる
        Storage::shouldReceive('cloud')
            ->once()
            ->andReturnNull();

        $response = $this->actingAs($this->user)
            ->json('POST', route('edit.myProfile',[
                'user' => $this->user->id,
            ]), [
                // ダミーファイルを作成して送信している
                'photo' => UploadedFile::fake()->image('photo.jpg'),
            ]);

        // レスポンスが500(INTERNAL SERVER ERROR)であること
        $response->assertStatus(500);

        // データベースに何も挿入されていないこと
        $this->assertEmpty(Photo::all());
    }
}
