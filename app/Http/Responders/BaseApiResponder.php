<?php

namespace App\Http\Responders;

use App\Http\Resources\BaseApiResource;

class BaseApiResponder
{
    public function success(array $data = [], string $message = 'Success', int $status = 200): BaseApiResource
    {
        $data = array_map(function ($item) {
            return $item instanceof \JsonSerializable ? $item->jsonSerialize() : $item;
        }, $data);

        return new BaseApiResource([
            'status' => $status,
            'message' => $message,
            'data' => $data,
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
