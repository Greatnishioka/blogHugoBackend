<?php

namespace App\Http\Responders;

use App\Http\Resources\BaseApiResource;
use App\Domain\AppUser\Entity\AppUserEntity;
use App\Functions\functions;

class RegisterArticlesResponder extends BaseApiResponder{
    
    public function success(array $data = [], string $message = 'Success', int $status = 200): BaseApiResource
    {

        $data = $data[0];
        $detail = $data->getDetail();
        $options = $data->getOptions();
        $tags = $data->getTags();
        $blocks = $data->getBlocks();
        $topImage = $detail->getTopImage();

        return new BaseApiResource([
            'status' => $status,
            'message' => $message,
            'data' => [
                'articleId' => $detail->getArticleId(),
                'detail' => [
                    'title' => $detail->getTitle(),
                    'note' => $detail->getNote(),
                    'topImage' => [
                        'imageUrl' => '',
                        'imageName' => '',
                        'altText' => '',
                    ],
                    'status' => $detail->getStatus(),
                ],
                'tags' => $tags,
                'blocks' => $blocks,
                'options' => $options,
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