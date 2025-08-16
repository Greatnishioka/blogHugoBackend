<?php

namespace App\Http\Responders\Auth;

use App\Http\Resources\BaseApiResource;
use App\Http\Responders\BaseApiResponder;

class LogoutResponder extends BaseApiResponder
{

    public function success(array $data = [], string $message = 'Success', int $status = 200): BaseApiResource
    {

        $data = $data[0];

        return new BaseApiResource([
            'status' => $status,
            'message' => $message,
            'data' => []
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