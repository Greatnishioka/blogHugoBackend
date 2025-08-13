<?php

namespace App\Http\Responders\Articles\Images;

use App\Http\Responders\BaseApiResponder;
use App\Http\Resources\BaseApiResource;

class ImagesSaveResponder extends BaseApiResponder{
    
    public function success(array $data = [], string $message = 'Success', int $status = 200): BaseApiResource
    {

        // $formattedData = array_map(function ($article) {
            
        //     return $article->getImages();

        // }, $data);

        return new BaseApiResource([
            'status' => $status,
            'message' => $message,
            'data' => $data,
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