<?php

namespace App\Http\Actions\Users;

use App\Domain\Users\UseCase\RegisterUseCase;
use App\Domain\Auth\UseCase\ExistenceCheckEmailUseCase;
use App\Domain\Auth\UseCase\RegisterAuthUseCase;

use App\Exceptions\BaseException;
use App\Http\Requests\Users\RegisterRequest;
use App\Http\Resources\BaseApiResource;
use App\Http\Responders\Users\RegisterResponder;

class Register
{
    private RegisterUseCase $useCase;
    private RegisterResponder $responder;
    private ExistenceCheckEmailUseCase $existenceCheckEmailUseCase;
    private RegisterAuthUseCase $registerAuthUseCase;

    public function __construct(
        RegisterUseCase $useCase,
        RegisterResponder $responder,
        ExistenceCheckEmailUseCase $existenceCheckEmailUseCase,
        RegisterAuthUseCase $registerAuthUseCase
    ) {
        $this->useCase = $useCase;
        $this->responder = $responder;
        $this->existenceCheckEmailUseCase = $existenceCheckEmailUseCase;
        $this->registerAuthUseCase = $registerAuthUseCase;
    }

    public function handle(RegisterRequest $request): BaseApiResource
    {
        try {

            $exists = $this->existenceCheckEmailUseCase->__invoke($request);
            if ($exists) {
                return $this->responder->error(
                    message: 'このメールアドレスはすでに登録されています。',
                    errors: ['email' => 'このメールアドレスはすでに登録されています。']
                );
            }

            $entities = $this->useCase->__invoke($request);
            // Auth情報は返さない
            $this->registerAuthUseCase->__invoke($request, $entities->getId());

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
