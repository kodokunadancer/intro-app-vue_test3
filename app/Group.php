<?php

declare(strict_types=1);

namespace App;

use Illuminate\Database\Eloquent\Model;
//Arrクラスを使用するため
use Illuminate\Support\Arr;

class Group extends Model
{
    const PASSWORD_LENGTH = 6;

    protected $visible = [
    'id', 'name', 'password', 'author_id', 'photo', 'users',
  ];

    //引数でpasswordの値を受け取る
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        if (!Arr::get($this->attributes, 'password')) {
            $this->setPassword();
        }
    }

    public function photo()
    {
        return $this->hasOne('App\Photo');
    }

    public function users()
    {
        return $this->belongsToMany('App\User');
    }

    private function setPassword(): void
    {
        $this->attributes['password'] = $this->getRandomPassword();
    }

    //実際にパスワード生成処理
    private function getRandomPassword()
    {
        //使用する文字列の用意
        $characters = array_merge(
            range(0, 9),
            range('a', 'z'),
            range('A', 'Z'),
            ['-', '_']
        );

        //配列の中の要素の数を数える
        $length = count($characters);

        //空の変数を用意
        $password = '';

        //ランダムな文字列を生成
        for ($i = 0; $i < self::PASSWORD_LENGTH; $i++) {
            $password .= $characters[random_int(0, $length - 1)];
        }

        return $password;
    }
}
