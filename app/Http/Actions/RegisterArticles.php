<?php

namespace App\Http\Actions;

use App\Domain\Articles\UseCase\RegisterArticlesUseCase;

use App\Exceptions\BaseException;
use App\Http\Requests\RegisterArticlesRequest;
use App\Http\Resources\BaseApiResource;
use App\Http\Responders\RegisterArticlesResponder;
use RuntimeException;

class RegisterArticles {
    private RegisterArticlesUseCase $useCase;
    private RegisterArticlesResponder $responder;

    public function __construct(RegisterArticlesUseCase $useCase, RegisterArticlesResponder $responder)
    {
        $this->useCase = $useCase;
        $this->responder = $responder;
    }

    public function handle(RegisterArticlesRequest $request): BaseApiResource
    {
        try {
            $entities = $this->useCase->__invoke($request);

            return $this->responder->success(
                data: [$entities],
                message: '記事の登録が完了しました。'
            );
        } catch (BaseException $e) {
            return $this->responder->error(
                message: '記事の登録の途中で予期せぬエラーが発生しました。',
                errors: ['exception' => $e->getMessage()]
            );
        }
    }
}
