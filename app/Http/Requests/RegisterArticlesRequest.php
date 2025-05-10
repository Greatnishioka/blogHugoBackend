<?php

namespace App\Http\Requests;

use App\Http\Requests\BaseApiRequest;
use App\Http\Requests\PiecesPositionRule;

class RegisterArticlesRequest extends BaseApiRequest {

    public function rules(): array
    {
        return [
            


        ];
    }

    public function messages(): array
    {
        return [
            
        ];
    }


    protected function passedValidation()
    {
        foreach ($this->all() as $key => $value) {
            if ($value === null) {
                $this->merge([$key => '']);
            }
        }
    }
}
