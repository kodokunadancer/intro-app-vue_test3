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

    //コメントを書いた人をたどる
    public function author()
    {
        return $this->belongsTo('App\Profile', 'active_profile_id', 'id', 'profiles');
    }

    //いいねを押した人をたどる
    public function likes()
    {
        return $this->belongsToMany('App\Profile', 'likes')->withTimestamps();
    }

    //そのコメントいくついいねがついているカウント
    public function getLikesCountAttribute()
    {
        return $this->likes->count();
    }

    //そのコメントにログインユーザー（プロフィール）がすでにいいねをおしているかチェック（真偽値が返る）
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
