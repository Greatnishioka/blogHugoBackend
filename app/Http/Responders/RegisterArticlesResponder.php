<?php

namespace App\Http\Responders;

use App\Http\Resources\BaseApiResource;
use App\Domain\AppUser\Entity\AppUserEntity;
use App\Functions\functions;

class RegisterArticlesResponder extends BaseApiResponder
{

    public function success(array $data = [], string $message = 'Success', int $status = 200): BaseApiResource
    {

        $data = $data[0];
        $detail = $data->getDetail();
        $options = $data->getOptions();
        $tags = $data->getTags();
        $blocks = $data->getBlocks();
        $topImage = $detail->getTopImage();

        if(!empty($blocks->getBlocks())) {
            $blocks = $this->shapingBlocks($blocks->getBlocks());
        }

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

    private function shapingBlocks(array $blocks): array
    {
        $blocksBox = [];
        $imagesBox = [];
        $linksBox = [];
        $codeBox = [];

        foreach ($blocks as $block) {

            $etc = $block->getEtc();
            switch ($block->getBlockType()) {
                case 'img':
                    $imagesBox[] = [
                        'blockUuid' => $block->getBlockUuid() ?? '',
                        'imageUrl' => $etc['image_url'] ?? '',
                        'imageName' => $etc['image_name'] ?? '',
                        'altText' => $etc['alt_text'] ?? '',
                    ];
                    break;
                
                case 'link':
                    $linksBox[] = [
                        'blockUuid' => $block->getBlockUuid() ?? '',
                        'url' => $etc['url'] ?? '',
                    ];
                    break;

                case 'code':
                    $codeBox[] = [
                        'blockUuid' => $block->getBlockUuid() ?? '',
                        'language' => $etc['language'] ?? '',
                        'code' => $etc['code'] ?? '',
                        'filename' => $etc['filename'] ?? null,
                    ];
                    break;

            }
            $blocksBox[] = [
                'blockUuid' => $block->getBlockUuid() ?? '',
                'parentBlockUuid' => $block->getParentBlockUuid() ?? null,
                'blockType' => $block->getBlockType() ?? '',
                'content' => $block->getContent() ?? '',
                'style' => $block->getStyle() ?? '',
            ];
        }

        return [
            'blocks' => $blocksBox,
            'images' => $imagesBox,
            'links' => $linksBox,
            'code' => $codeBox,
        ];
    }
}