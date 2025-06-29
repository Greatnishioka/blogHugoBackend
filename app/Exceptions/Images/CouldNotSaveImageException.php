<?php

namespace App\Exceptions\Images;

use App\Exceptions\BaseException;

class CouldNotSaveImageException extends BaseException
{
    protected $errors;
    public function __construct(string $message = '何かしらの理由により画像の保存が失敗しました。', array $errors = [], int $code = 500)
    {
        parent::__construct($message, $errors, $code);
        $this->errors = $errors;
    }
    public function getErrors(): array
    {
        return $this->errors;
    }
}