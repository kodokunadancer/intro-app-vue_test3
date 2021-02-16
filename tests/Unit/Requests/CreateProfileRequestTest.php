<?php

declare(strict_types=1);

namespace Tests\Unit\Requests;

use App\Http\Requests\CreateProfile;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class CreateProfileRequestTest extends TestCase
{
    /**
     * カスタムリクエストのバリデーション.
     * @test
     * @param array 項目名
     * @param array 値
     * @param bool 期待値
     * @param mixed $keys
     * @param mixed $values
     * @param mixed $expect
     * @dataProvider dataCreateProfile
     */
    public function should_プロフィール作成バリデーションが機能する($keys, $values, $expect): void
    {
        $dataList = array_combine($keys, $values);

        $request = new CreateProfile();

        $rules = $request->rules();

        $validator = Validator::make($dataList, $rules);

        $result = $validator->passes();

        $this->assertEquals($result, $expect);
    }

    public function dataCreateProfile()
    {
        return  [
            '正常' => [
                ['name', 'introduction'],
                ['test_name', 'hello'],
                true,
             ],
            '必須エラー' => [
                ['name', 'introduction'],
                ['', ''],
                false,
             ],
            '最大文字数エラー' => [
                ['name', 'introduction'],
                [str_repeat('a', 21), str_repeat('a', 501)],
                false,
             ],
        ];
    }
}
