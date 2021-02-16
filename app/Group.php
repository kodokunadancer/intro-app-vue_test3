<?php

declare(strict_types=1);

namespace App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class Group extends Model
{
    protected $visible = [
        'id', 'name', 'password', 'author_id', 'photo', 'users', 'author',
    ];

    const PASSWORD_LENGTH = 6;

    /**
     * 引数でランダムなパスワードを受け取る
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        if (!Arr::get($this->attributes, 'password')) {
            $this->setPassword();
        }
    }

    /**
     * ランダムなpasswordをpassword属性に代入する
     */
    private function setPassword(): void
    {
        $this->attributes['password'] = $this->getRandomPassword();
    }

    /**
     * ランダムなpasswordを生成する
     * @return string
     */
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

    /**
    * リレーションシップ - phptosテーブル
     * @return \Illuminate\Database\Eloquent\Relations\hasOne
     */
    public function photo()
    {
        return $this->hasOne('App\Photo');
    }

    /**
     * リレーションシップ - usersテーブル
     * @return \Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function users()
    {
        return $this->belongsToMany('App\User');
    }

    /**
     * リレーションシップ - usersテーブル
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function author()
    {
        return $this->belongsTo('App\User', 'author_id', 'users');
    }

}
