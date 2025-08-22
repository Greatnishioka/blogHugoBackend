<?php

namespace App\Http\Requests;

use App\Http\Requests\BaseApiRequest;

// enums
use App\Enums\BlockTypeEnum;

use Illuminate\Validation\Rules\Enum;

class RegisterArticlesRequest extends BaseApiRequest
{

    public function rules(): array
    {
        return [
            'blocks' => 'required|array',
            'blocks.baseBlocks' => 'required|array',
            'blocks.baseBlocks.*.blockUuid' => 'required|uuid',
            'blocks.baseBlocks.*.blockType' => [new Enum(BlockTypeEnum::class)],
            'blocks.baseBlocks.*.content' => 'required|string',
            'blocks.baseBlocks.*.parentBlockUuid' => 'nullable|uuid',
            'blocks.baseBlocks.*.orderFromParentBlock' => 'nullable|integer',
            'blocks.baseBlocks.*.blockStyle' => 'nullable|string',

            'blocks.images' => 'nullable|array',
            'blocks.images.*.blockUuid' => 'required|uuid',
            'blocks.images.*.imageUrl' => 'required|url',
            'blocks.images.*.imageName' => 'required|string',
            'blocks.images.*.altText' => 'required|string',

            'detail.userInfo.userUuid' => 'required|uuid',
            'detail.title' => 'required|string|max:255',
            'detail.description' => 'required|string|max:1000',
            'detail.note' => 'nullable|string',

            'status' => 'required|array',
            'status.*.statusId' => 'required|integer',
            'status.*.statusValue' => 'required|boolean',

            'tags.tag1' => 'required|string', // 1個目のタグは必須
            'tags.tag2' => 'nullable|string',
            'tags.tag3' => 'nullable|string',
            'tags.tag4' => 'nullable|string',
            'tags.tag5' => 'nullable|string',

            'options' => 'required|array',
            'options.*.optionId' => 'required|integer',
            'options.*.optionValue' => 'required|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'blocks.required' => 'ブロック情報は必須です。',
            'blocks.baseBlocks.required' => 'ベースブロック情報は必須です。',
            'blocks.baseBlocks.*.blockUuid.required' => 'ブロックUUIDは必須です。',
            'blocks.baseBlocks.*.blockType.required' => 'ブロックタイプは必須です。',
            'blocks.baseBlocks.*.content.required' => 'コンテンツは必須です。',
            'blocks.images.required' => '画像ブロック情報は必須です。',
            'blocks.images.*.blockUuid.required' => '画像ブロックUUIDは必須です。',
            'blocks.images.*.imageUrl.required' => '画像URLは必須です。',
            'blocks.images.*.imageName.required' => '画像名は必須です。',
            'blocks.images.*.altText.required' => '代替テキストは必須です。',
            'detail.userUuid.required' => 'ユーザーUUIDは必須です。',
            'detail.title.required' => 'タイトルは必須です。',
            'status.required' => 'ステータス情報は必須です。',
            'tags.tag1.required' => '1個目のタグは必須です。',
            'options.required' => 'オプション情報は必須です。',

        ];
    }
}
