<?php

namespace App\Http\Actions\Users;

use App\Domain\Users\UseCase\RegisterUseCase;

use App\Exceptions\BaseException;
use App\Http\Requests\Users\RegisterRequest;
use App\Http\Resources\BaseApiResource;
use App\Http\Responders\Users\RegisterResponder;

class Register {
    private RegisterUseCase $useCase;
    private RegisterResponder $responder;

    public function __construct(RegisterUseCase $useCase, RegisterResponder $responder)
    {
        $this->useCase = $useCase;
        $this->responder = $responder;
    }

    public function handle(RegisterRequest $request): BaseApiResource
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
