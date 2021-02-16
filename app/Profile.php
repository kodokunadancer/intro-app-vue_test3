<?php

declare(strict_types=1);

namespace App;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $visible = [
      'id', 'user_id', 'name', 'introduction', 'owner', 'photos', 'comments',
    ];

    /**
     * リレーションシップ - usersテーブル
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function owner()
    {
        return $this->belongsTo('App\User', 'user_id', 'id', 'users');
    }

    /**
     * リレーションシップ - photosテーブル
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function photos()
    {
        return $this->hasMany('App\Photo');
    }

    /**
     * リレーションシップ - commentsテーブル
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function comments()
    {
        return $this->hasMany('App\Comment', 'passive_profile_id', 'id', 'comments');
    }
}
