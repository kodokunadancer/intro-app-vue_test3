<?php

declare(strict_types=1);

namespace App;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $visible = [
      'id', 'user_id', 'name', 'introduction', 'photos', 'comments',
    ];

    public function owner()
    {
        return $this->belongsTo('App\User', 'user_id', 'id', 'users');
    }

    public function photos()
    {
        return $this->hasMany('App\Photo');
    }

    public function comments()
    {
        return $this->hasMany('App\Comment', 'passive_profile_id', 'id', 'comments');
    }
}
