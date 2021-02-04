<?php

declare(strict_types=1);

namespace App\Http\Controllers;

//モデル
use App\Group;
use App\Http\Requests\CreateGroup;
use App\Http\Requests\EditGroup;
use App\Http\Requests\ReserchGroup;
//フォームリクエスト
use App\Photo;
use App\Profile;
use App\User;
//ファイル処理
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
//Authクラス
use Illuminate\Support\Facades\Storage;

//Gateクラス

class GroupController extends Controller
{
    //該当グループ取得
    public function getGroup(Group $group)
    {
        $editGroup = Group::where('id', $group->id)->with('photo')->first();
        return $editGroup;
    }

    //グループ作成処理
    public function create(User $user, CreateGroup $request)
    {
        $group = new Group();
        $group->name = $request->name;
        $group->password = $group->password;
        $group->author_id = $user->id;
        $group->save();

        $user->groups()->attach($group);

        return $group;
    }

    //グループ検索処理
    public function reserch(User $user, ReserchGroup $request)
    {
        $group = Group::where([
        ['name', $request->group_name],
        ['password', $request->password],
      ])->with('photo')->first();

        if ($group) {
            return $group;
        }
        return false;
    }

    //グループ参加処理
    public function join(User $user, Group $group)
    {
        //ユーザーとグループの紐付きを中間テーブルに保存する
        //すでに同じグループに参加している場合は、ロールバックする
        try {
            $user->groups()->attach($group);
            return $group;
        } catch (\Exception $exception) {
            DB::rollback();
            throw $exception;
        }
    }

    //グループ一覧
    public function index(User $user)
    {
        if ($user->groups) {
            $my_groups = $user->groups()->with('photo')->get();
            return $my_groups;
        }
        return false;
    }

    //グループ編集処理
    public function edit(User $user, Group $group, EditGroup $request)
    {
        if ($request->photo) {
            $extension = $request->photo->extension();

            $group_photo = new Photo();

            // インスタンス生成時に割り振られたランダムなID値と
            // 本来の拡張子を組み合わせてファイル名とする
            $group_photo->filename = $group_photo->random_id . '.' . $extension;

            $group_photo->filename = Storage::cloud()->putFileAs('vue', $request->photo, $group_photo->filename, 'public');

            // データベースエラー時にファイル削除を行うため
            // トランザクションを利用する
            DB::beginTransaction();

            try {
                $group->photo()->delete();
                $group->photo()->save($group_photo);
                DB::commit();
            } catch (\Exception $exception) {
                DB::rollBack();
                // DBとの不整合を避けるためアップロードしたファイルを削除
                Storage::cloud()->delete($group_photo->filename);
                throw $exception;
            }
        }

        $group->name = $request->name;
        $group->save();

        return response(201);
    }

    //グループ詳細
    public function show(User $user, Group $group)
    {
        // 参加するグループと紐付いている複数のユーザーを配列に格納
        foreach ($group->users as $users) {
            $group_users[] = $users;
        }
        // 取り出した複数ユーザーのプロフィールをひとつずつ取り出す
        foreach ($group_users as $group_user) {
            $profiles[] = Profile::where('user_id', $group_user->id)->with('photos')->first();
        }
        // プロフィールのnameキーと値のセットを別の配列へ一旦まとめて格納。idについてもまとめておく。
        foreach ($profiles as $value) {
            $name_array[] = $value['name'];
            $id_array[] = $value['id'];
        }
        // profiles多次元配列をnameの五十音順を軸に並び替える
        array_multisort($name_array, SORT_ASC, SORT_STRING, $id_array, SORT_ASC, SORT_NUMERIC, $profiles);
        //自分のプロフィールを取得
        $my_profile = Profile::where('user_id', $user->id)->with('photos')->first();
        return [$group, $my_profile, $profiles];
    }

    //グループから退会
    public function exit(User $user, Group $group)
    {
        $group->users()->detach($user);
        return response(200);
    }

    //グループの削除
    public function delete(User $user, Group $group)
    {
        $group->delete();
        return response(200);
    }

    //グループから強制退会
    public function force(User $user, Group $group, Profile $profile)
    {
        $exit_user = $profile->owner()->first();
        $group->users()->detach($exit_user);
        return response(200);
    }
}
