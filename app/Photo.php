<?php

declare(strict_types=1);

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class Photo extends Model
{
    protected $appends = [
      'url',
    ];

    protected $visible = [
      'random_id', 'url',
    ];

    protected $kyeType = 'string';

    public $incrementing = false;

    protected $primaryKey = 'random_id';

    const ID_LENGTH = 12;

    /**
     * 引数でランダムなrandom_idを受け取る
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        if (!Arr::get($this->attributes, 'random_id')) {
            $this->setId();
        }
    }

    /**
     * random_idをrandom_id属性に代入する
     */
    private function setId(): void
    {
        $this->attributes['random_id'] = $this->getRandomId();
    }

    /**
     * ランダムなidを生成する
     * @return string
     */
    private function getRandomId()
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
        $random_id = '';

        //ランダムな文字列を生成
        for ($i = 0; $i < self::ID_LENGTH; $i++) {
            $random_id .= $characters[random_int(0, $length - 1)];
        }

        return $random_id;
    }

    /**
     * アクセサ - url
     * @return string
     */
    public function getUrlAttribute()
    {
        return Storage::cloud()->url($this->attributes['filename']);
    }
}
