<?php

namespace App\Http\Requests\Users;

use App\Http\Requests\BaseApiRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends BaseApiRequest
{

    public function rules(): array
    {
        return [
            'mailadress' => ['required', 'string', 'email'],
            'password' => [
                'required',
                'string',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->letters()
                    ->numbers()
                    ->symbols()
            ],
            'userData' => ['required', 'array'],
            'userData.name' => ['required', 'string', 'max:255'],
            'userData.icon_url' => ['nullable', 'string', 'max:255'],
            'userData.bio' => ['nullable', 'string', 'max:500'],
            'userData.occupation' => ['required', 'string', 'in:student,engineer,designer,manager,other'],

        ];
    }

    public function messages(): array
    {
        return [
            'mailadress.required' => 'メールアドレスは必須です。',
            'mailadress.string' => 'メールアドレスは文字列でなければなりません。',
            'mailadress.email' => 'メールアドレスの形式が正しくありません。',
            'password.required' => 'パスワードは必須です。',
            'password.string' => 'パスワードは文字列でなければなりません。',
            'password.confirmed' => 'パスワードの確認が一致しません。',
            'userData.required' => 'ユーザーデータは必須です。',
            'userData.array' => 'ユーザーデータは配列でなければなりません。',
            'userData.*.name.required' => 'ユーザー名は必須です。',
            'userData.*.name.string' => 'ユーザー名は文字列でなければなりません。',
            'userData.*.name.max' => 'ユーザー名は255文字以内でなければなりません。',
            'userData.*.icon_url.string' => 'アイコンURLは文字列でなければなりません。',
        ];
    }
}
