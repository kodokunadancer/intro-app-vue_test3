<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditGroup extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
             'photo' => 'file|mimes:jpg,jpeg,png,gif,heic',
             'name' => 'required|max:20',
         ];
    }

    public function attributes()
    {
        return [
         'photo' => '画像ファイル',
         'name' => 'グループ名',
       ];
    }
}
