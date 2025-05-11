<?php

namespace App\Http\Responders;

use App\Http\Resources\BaseApiResource;
use App\Domain\AppUser\Entity\AppUserEntity;
use App\Functions\functions;

class RegisterArticlesResponder extends BaseApiResponder{
    
    public function __construct()
    {
    }
    public function success(array $data = [], string $message = 'Success', int $status = 200): BaseApiResource
    {

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