<?php

namespace App\Exceptions\Articles;

use App\Exceptions\BaseException;

class AlreadyExistIdException extends BaseException
{
    protected $errors;
    public function __construct(string $message = '既に存在するIDです。', array $errors = [], int $code = 409)
    {
        parent::__construct($message, $errors, $code);
        $this->errors = $errors;
    }
    public function getErrors(): array
    {
        return $this->errors;
    }
}