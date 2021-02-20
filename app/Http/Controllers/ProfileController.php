<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Comment;
use App\Group;
use Illuminate\Http\Request;
use App\Http\Requests\CreateProfile;
use App\Http\Requests\EditProfile;
use App\Http\Requests\StoreComment;
use App\Photo;
use App\Profile;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * プロフィール作成.
     * @param CreateProfile $request
     * @return User
     */
    public function create(CreateProfile $request)
    {
        $my_profile = new Profile();
        $my_profile->name = $request->name;
        $my_profile->introduction = $request->introduction;
        $user = Auth::user();
        $user->profiles()->save($my_profile);
        return response($user, 201);
    }

    /**
     * マイプロフィール詳細表示.
     * @param User $user
     * @return Profile
     */
    public function showMyProfile(User $user)
    {
        $myProfile = Profile::where('user_id', $user->id)->with(['photos', 'comments', 'comments.author', 'comments.likes'])->first();
        return $myProfile;
    }

    /**
     * プロフィール編集.
     * @param User        $user
     * @param EditProfile $request
     * @return \Illuminate\Http\Response
     */
    public function editMyProfile(User $user, EditProfile $request)
    {
        $profile = $user->profiles()->first();

        //画像編集処理
        if ($request->photo) {

            $extension = $request->photo->extension();

            $profile_photo = new Photo();

            // インスタンス生成時に割り振られたランダムなID値と
            // 本来の拡張子を組み合わせてファイル名とする
            $profile_photo->filename = $profile_photo->random_id . '.' . $extension;

            $profile_photo->filename = Storage::cloud()->putFileAs('profiles', $request->photo, $profile_photo->filename, 'public');

            // データベースエラー時にファイル削除を行うため
            // トランザクションを利用する
            DB::beginTransaction();

            try {
                $profile->photos()->delete();
                $profile->photos()->save($profile_photo);
                DB::commit();
            } catch (\Exception $exception) {
                DB::rollBack();
                // DBとの不整合を避けるためアップロードしたファイルを削除
                Storage::cloud()->delete($profile_photo->filename);
                throw $exception;
            }
        }

        //名前と自己紹介の編集処理
        if ($user->id == $profile->user_id) {
            $profile->name = $request->textName;
            $profile->introduction = $request->textIntroduction;
            $profile->save();
        }

        return response($profile, 201);
    }

    /**
     * コメント投稿（マイページ）.
     * @param User         $user
     * @param StoreComment $request
     * @return \Illuminate\Http\Response
     */
    public function addMyComment(User $user, StoreComment $request)
    {
        // コメントしたプロフィールを取得
        $active_profile = $user->profiles()->first();
        // コメントを受けた側とした側は同じ
        $passive_profile = $active_profile;

        $comment = new Comment();
        $comment->content = $request->get('content');
        // まずはコメントした側のプロフィール情報を保存
        $comment->active_profile_id = $active_profile->id;
        // 親から子へのリレーションの保存の仕方でなければならない
        // 次はコメントを受けた側のプロフィールをさらに紐付けてそのコメントを保存
        $passive_profile->comments()->save($comment);

        $new_comments = Comment::where('passive_profile_id', $passive_profile->id)->with(['author', 'likes'])->get();
        return response($new_comments, 201);
    }

    /**
     * いいね付与（マイページ）.
     * @param User    $user
     * @param Comment $comment
     * @return array
     */
    public function myLike(User $user, Comment $comment)
    {
        $profile = $user->profiles()->first();
        $comment = Comment::where('id', $comment->id)->with('likes')->first();

        if (!$comment) {
            abort(404);
        }

        //いいねは１度しか押せない
        $comment->likes()->detach($profile->id);
        $comment->likes()->attach($profile->id);

        return ['comment_id' => $comment->id];
    }

    /**
     * いいね削除（マイページ）.
     * @param User    $user
     * @param Comment $comment
     * @return array
     */
    public function myUnlike(User $user, Comment $comment)
    {
        $profile = $user->profiles()->first();
        $comment = Comment::where('id', $comment->id)->with('likes')->first();

        if (!$comment) {
            abort(404);
        }

        $comment->likes()->detach($profile->id);

        return ['comment_id' => $comment->id];
    }

    /**
     * プロフィール詳細表示.
     * @param User    $user
     * @param Group   $group
     * @param Profile $profile
     * @return Profile
     */
    public function showProfile(User $user, Group $group, Profile $profile)
    {
        //profilesデーブルの中でクリックされたプロフィールデータを返す
        $otherProfile = Profile::where('id', $profile->id)->with(['photos', 'comments', 'comments.author', 'comments.likes'])->first();
        return $otherProfile;
    }

    /**
     * コメント保存.
     * @param User         $user
     * @param Group        $group
     * @param Profile      $profile
     * @param StoreComment $request
     * @return \Illuminate\Http\Response
     */
    public function addComment(User $user, Group $group, Profile $profile, StoreComment $request)
    {
        //コメントしたプロフィールを取得
        $active_profile = $user->profiles()->first();
        // コメントを受けた側のプロフィールをわかりやすいように変換
        $passive_profile = $profile;

        $comment = new Comment();
        $comment->content = $request->get('content');
        //まずはコメントした側のプロフィール情報を保存
        $comment->active_profile_id = $active_profile->id;
        //親から子へのリレーションの保存の仕方でなければならない
        //次はコメントを受けた側のプロフィールをさらに紐付けてそのコメントを保存
        $passive_profile->comments()->save($comment);

        $new_comments = Comment::where('passive_profile_id', $passive_profile->id)->with(['author', 'likes'])->get();
        return response($new_comments, 201);
    }

    /**
     * いいね付与.
     * @param User    $user
     * @param Group   $group
     * @param Profile $profile
     * @param Comment $comment
     * @return array
     */
    public function like(User $user, Group $group, Profile $profile, Comment $comment)
    {
        //まずいいねを押したプロフィールのオブジェクトを取得
        $profile = $user->profiles()->first();
        $comment = Comment::where('id', $comment->id)->with('likes')->first();

        if (!$comment) {
            abort(404);
        }

        //いいねは１回しか押させない
        $comment->likes()->detach($profile->id);
        $comment->likes()->attach($profile->id);

        return ['comment_id' => $comment->id];
    }

    /**
     * いいね削除.
     * @param User    $user
     * @param Group   $group
     * @param Profile $profile
     * @param Comment $comment
     * @return array
     */
    public function unlike(User $user, Group $group, Profile $profile, Comment $comment)
    {
        //まずいいねを押したプロフィールのオブジェクトを取得
        $profile = $user->profiles()->first();
        $comment = Comment::where('id', $comment->id)->with('likes')->first();

        if (!$comment) {
            abort(404);
        }

        $comment->likes()->detach($profile->id);

        return ['comment_id' => $comment->id];
    }
}
