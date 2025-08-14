<?php

namespace App\Http\Actions\Auth;

use App\Domain\Auth\UseCase\LoginUseCase;

use App\Exceptions\BaseException;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\BaseApiResource;
use App\Http\Responders\Auth\LoginResponder;

class Login {
    private LoginUseCase $useCase;
    private LoginResponder $responder;

    public function __construct(LoginUseCase $useCase, LoginResponder $responder)
    {
        $this->useCase = $useCase;
        $this->responder = $responder;
    }

    public function handle(LoginRequest $request): BaseApiResource
    {
        try {
            $entities = $this->useCase->__invoke($request);

            return $this->responder->success(
                data: [$entities],
                message: '画像の登録が完了しました。'
            );
        } catch (BaseException $e) {
            return $this->responder->error(
                message: '画像の登録の途中で予期せぬエラーが発生しました。',
                errors: ['exception' => $e->getMessage()]
            );
        }
    }
}
