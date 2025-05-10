<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BaseApiResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'status' => $this->resource['status'] ?? 200,
            'message' => $this->resource['message'] ?? 'Success',
            'data' => $this->resource['data'] ?? null,
            'errors' => $this->resource['errors'] ?? null,
        ];
    }
}
