<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\BaseApiRequest;

class LogoutRequest extends BaseApiRequest
{

    public function rules(): array
    {
        return [
            'userAuth.email' => 'required|email',
            'userAuth.password' => 'required|string|min:8|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'userAuth.email.required' => 'メールアドレスは必須です。',
            'userAuth.email.email' => 'メールアドレスの形式が正しくありません。',
            'userAuth.password.required' => 'パスワードは必須です。',
            'userAuth.password.min' => 'パスワードは8文字以上でなければなりません。',
            'userAuth.password.max' => 'パスワードは255文字以内でなければなりません。',
        ];
    }
}
