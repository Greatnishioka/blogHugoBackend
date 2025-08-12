<?php

namespace App\Http\Responders\Articles\Project;

use App\Http\Resources\BaseApiResource;
use App\Http\Responders\BaseApiResponder;

class GetInitProjectResponder extends BaseApiResponder
{

    public function success(array $data = [], string $message = 'Success', int $status = 200): BaseApiResource
    {

        $data = $data[0];
        $detail = $data->getDetail();
        $topImage = $detail->getTopImage();

        return new BaseApiResource([
            'status' => $status,
            'message' => $message,
            'data' => [
                'id' => null,
                'articleId' => null,
                'detail' => [
                    'articleId' => null,
                    'title' => null,
                    'author' => $detail->getAuthor(),
                    'authorId' => 1,
                    'topImage' => [
                        'imageUrl' => '',
                        'imageName' => '',
                        'altText' => '',
                    ],
                    'status' => $detail->getStatus(),
                ],
                'tags' => [
                    'articleId' => null,
                    'tags' => [],
                ],
                'blocks' => [
                    'articleId' => null,
                    'blocks' => [],
                ],
                'options' => $data->getOptions(),
            ]
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