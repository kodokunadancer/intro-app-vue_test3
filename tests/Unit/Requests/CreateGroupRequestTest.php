<?php

declare(strict_types=1);

namespace Tests\Unit\Requests;

use App\Http\Requests\CreateGroup;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class CreateGroupRequestTest extends TestCase
{
    /**
     * カスタムリクエストのバリデーション.
     * @test
     * @param string 項目名
     * @param string 値
     * @param bool 期待値
     * @param mixed $item
     * @param mixed $data
     * @param mixed $expect
     * @dataProvider dataCreateGroup
     */
    public function should_グループ作成バリデーションが機能する($item, $data, $expect): void
    {
        $dataList = [$item => $data];

        $request = new CreateGroup();

        $rules = $request->rules();

        $validator = Validator::make($dataList, $rules);

        $result = $validator->passes();

        $this->assertEquals($result, $expect);
    }

    public function dataCreateGroup()
    {
        return  [
            '正常' => ['name', 'hello', true],
            '必須エラー' => ['name', '', false],
            '最大文字数エラー' => ['name', str_repeat('a', 21), false],
        ];
    }
}
