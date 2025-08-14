<?php

namespace App\Http\Responders\Auth;

use App\Http\Resources\BaseApiResource;
use App\Http\Responders\BaseApiResponder;

class LoginResponder extends BaseApiResponder
{

    public function success(array $data = [], string $message = 'Success', int $status = 200): BaseApiResource
    {

        $data = $data[0];

        return new BaseApiResource([
            'status' => $status,
            'message' => $message,
            'data' => [
                // フロントエンド側ではuserIdとして扱う
                'userId' => $data->getUserUuid(),
            ]
        ]);
    }

    public function error(string $message, ?array $errors = null, int $status = 422): BaseApiResource
    {
        return new BaseApiResource([
            'status' => $status,
            'message' => $message,
            'errors' => $errors,
        ]);
    }
}