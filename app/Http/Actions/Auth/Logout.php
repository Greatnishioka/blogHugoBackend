<?php

namespace App\Http\Actions\Auth;

use App\Domain\Auth\UseCase\LogoutUseCase;

use App\Exceptions\BaseException;
use App\Http\Requests\Auth\LogoutRequest;
use App\Http\Resources\BaseApiResource;
use App\Http\Responders\Auth\LogoutResponder;

class Logout {
    private LogoutUseCase $useCase;
    private LogoutResponder $responder;

    public function __construct(LogoutUseCase $useCase, LogoutResponder $responder)
    {
        $this->useCase = $useCase;
        $this->responder = $responder;
    }

    public function handle(LogoutRequest $request): BaseApiResource
    {
        try {
            $entities = $this->useCase->__invoke($request);

            return $this->responder->success(
                data: [$entities],
                message: 'ログアウトが完了しました。'
            );
        } catch (BaseException $e) {
            return $this->responder->error(
                message: 'ログアウトの途中で予期せぬエラーが発生しました。',
                errors: ['exception' => $e->getMessage()]
            );
        }
    }
}
