<?php

namespace App\Http\Requests\Articles\Images;

use App\Http\Requests\BaseApiRequest;

class ImagesSaveRequest extends BaseApiRequest
{

    public function rules(): array
    {
        return [
            'file' => 'required',
            'file.*' => 'image|mimes:jpeg,png,webp,jpg,gif|max:5120', // 一旦5mbまで
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => '画像ファイルは必須です。',
            'file.*.image' => '画像ファイルは画像形式でなければなりません。',
            'file.*.mimes' => '画像ファイルはjpeg, png, webp, jpg, gifのいずれかの形式でなければなりません。',
            'file.*.max' => '画像ファイルは最大5mbまでです。',
        ];
    }
}
