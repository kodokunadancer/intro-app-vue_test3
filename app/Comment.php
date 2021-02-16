<?php

declare(strict_types=1);

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Comment extends Model
{
    protected $appends = [
      'likes_count', 'liked_by_user',
    ];

    protected $visible = [
      'id', 'content', 'author', 'likes', 'likes_count', 'liked_by_user',
    ];

    /**
     * リレーションシップ - profilesテーブル
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function author()
    {
        return $this->belongsTo('App\Profile', 'active_profile_id', 'id', 'profiles');
    }

    /**
     * リレーションシップ - profilesテーブル
     * @return \Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function likes()
    {
        return $this->belongsToMany('App\Profile', 'likes')->withTimestamps();
    }

    /**
     * アクセサ - likes_count
     * @return integer
     */
    public function getLikesCountAttribute()
    {
        return $this->likes->count();
    }

    /**
     * そのコメントにログインユーザー（プロフィール）がすでにいいねをおしているかチェック
     * アクセサ - liked_by_user
     * @return boolean
     */
    public function getLikedByUserAttribute()
    {
        if (Auth::guest()) {
            return false;
        }

        // $my_profileは、containsの関数内で定義しなければ、未定義となった。ここ重要。苦労した。
        return $this->likes->contains(function ($profile) {
            $my_profile = Auth::user()->profiles()->first();
            return $profile->id === $my_profile->id;
        });
    }
}
