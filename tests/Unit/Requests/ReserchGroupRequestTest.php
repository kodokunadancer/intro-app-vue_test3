<?php

declare(strict_types=1);

namespace Tests\Unit\Requests;

use App\Http\Requests\ReserchGroup;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class ReserchGroupRequestTest extends TestCase
{
    /**
     * カスタムリクエストのバリデーション.
     * @test
     * @param array 項目名
     * @param array 値
     * @param bool 期待値
     * @param mixed $kyes
     * @param mixed $values
     * @param mixed $expect
     * @dataProvider dataReserchGroup
     */
    public function should_グループ検索バリデーションが機能する($kyes, $values, $expect): void
    {
        $dataList = array_combine($kyes, $values);

        $request = new ReserchGroup();

        $rules = $request->rules();

        $validator = Validator::make($dataList, $rules);

        $result = $validator->passes();

        $this->assertEquals($result, $expect);
    }

    public function dataReserchGroup()
    {
        return  [
            '正常' => [
                ['group_name', 'password'],
                ['test_name', 'test1234'],
                true,
             ],
            '必須エラー' => [
                ['name', 'password'],
                ['', ''],
                false,
             ],
            '最大文字数エラー' => [
                ['name', 'password'],
                [str_repeat('a', 21), str_repeat('a', 5)],
                false,
             ],
         ];
    }
}
