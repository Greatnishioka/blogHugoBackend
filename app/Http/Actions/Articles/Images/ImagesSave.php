<?php

namespace App\Http\Actions\Articles\Images;

use App\Domain\Articles\UseCase\ImagesSaveUseCase;

use App\Exceptions\BaseException;
use App\Http\Requests\Articles\Images\ImagesSaveRequest;
use App\Http\Resources\BaseApiResource;
use App\Http\Responders\Articles\Images\ImagesSaveResponder;;

class ImagesSave {
    private ImagesSaveUseCase $useCase;
    private ImagesSaveResponder $responder;

    public function __construct(ImagesSaveUseCase $useCase, ImagesSaveResponder $responder)
    {
        $this->useCase = $useCase;
        $this->responder = $responder;
    }

    public function handle(ImagesSaveRequest $request): BaseApiResource
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
