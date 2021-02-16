<?php

declare(strict_types=1);

namespace Tests\Unit\Requests;

use App\Http\Requests\StoreComment;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class StoreCommentRequestTest extends TestCase
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
     * @dataProvider dataStoreComment
     */
    public function should_コメント投稿バリデーションが機能する($item, $data, $expect): void
    {
        $dataList = [$item => $data];

        $request = new StoreComment();

        $rules = $request->rules();

        $validator = Validator::make($dataList, $rules);

        $result = $validator->passes();

        $this->assertEquals($result, $expect);
    }

    public function dataStoreComment()
    {
        return  [
            '正常' => ['content', 'hello', true],
            '必須エラー' => ['content', '', false],
            '最大文字数エラー' => ['content', str_repeat('a', 501), false],
        ];
    }
}
