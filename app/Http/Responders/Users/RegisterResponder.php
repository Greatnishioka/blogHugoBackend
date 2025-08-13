<?php

namespace App\Http\Responders\Users;

use App\Http\Resources\BaseApiResource;
use App\Http\Responders\BaseApiResponder;

class RegisterResponder extends BaseApiResponder
{

    public function success(array $data = [], string $message = 'Success', int $status = 200): BaseApiResource
    {

        $data = $data[0];
        $userData = $data->getUserData();
        $userOption = $data->getUserOption();
        $userStatus = $data->getUserStatus();

        return new BaseApiResource([
            'status' => $status,
            'message' => $message,
            'data' => [
                'userUuid' => $data->getUserUuid(),
                'userData' => $userData ? $userData->jsonSerialize() : null,
                'userOption' => $userOption ? array_map(fn($option) => $option->jsonSerialize(), $userOption) : null,
                'userStatus' => $userStatus ? array_map(fn($status) => $status->jsonSerialize(), $userStatus) : null,
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