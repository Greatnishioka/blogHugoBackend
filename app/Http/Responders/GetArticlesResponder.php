<?php

namespace App\Http\Responders;

use App\Http\Resources\BaseApiResource;
use App\Domain\AppUser\Entity\AppUserEntity;
use App\Functions\functions;

class GetArticlesResponder extends BaseApiResponder{
    
    public function success(array $data = [], string $message = 'Success', int $status = 200): BaseApiResource
    {

        $formattedData = array_map(function ($article) {
            return [
                // かなり構造をがっつり変えたので、後ほど調整

                // 'id' => $article->getId(),
                // 'title' => $article->getTitle(),
                // 'author' => $article->getAuthor(),
                // 'authorId' => $article->getAuthorId(),
                // 'viewCount' => $article->getViewCount(),
                // 'blocks' => array_map(function ($block) {
                //     return [
                //         'id' => $block->getId(),
                //         'articleId' => $block->getArticleId(),
                //         'parentBlockId' => $block->getParentBlockUuid(),
                //         'blockType' => $block->getBlockType(),
                //         'content' => $block->getContent(),
                //         'style' => $block->getStyle(),
                //         'url' => $block->getUrl(),
                //         'language' => $block->getLanguage(),
                //     ];
                // }, $article->getBlocks() ?? []),
            ];
        }, $data);

        return new BaseApiResource([
            'status' => $status,
            'message' => $message,
            'data' => $formattedData,
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