<?php

namespace App\Domain\Articles\Infrastructure;
use Illuminate\Http\Request;
// Models
use App\Models\Articles\Article;
use App\Models\Articles\ArticleBlock;
use App\Models\Articles\ArticleDetail;
use App\Models\User\User as User;
use App\Models\Articles\ArticleOption;
use App\Models\Articles\ArticleStatus;
use App\Models\Articles\ArticleTag;
use App\Models\Articles\Blocks\BlockImage;
use App\Models\Status\Status;
use App\Models\Options\Option;
use App\Models\Articles\Blocks\BlockTypes;
use App\Models\Tags\Tag;
// Entities
use App\Domain\Articles\Entity\ArticlesEntity;
use App\Domain\Articles\Entity\ArticleOptionsEntity;
use App\Domain\Articles\Entity\ArticleDetailEntity;
use App\Domain\Articles\Entity\ArticleStatusEntity;
use App\Domain\Articles\Entity\ArticleBlockEntity;
use App\Domain\Articles\Entity\ArticleTagsEntity;
use App\Domain\Articles\Entity\Blocks\BlockEntity;
use App\Domain\Articles\Entity\Status\StatusEntity;
use App\Domain\Articles\Entity\Option\OptionEntity;
use App\Domain\Articles\Entity\Images\ImagesEntity;
use App\Domain\Articles\Entity\Images\ImageEntity;
// Repositories
use App\Domain\Articles\Repository\ArticlesRepository;
// DTOs
use App\Domain\Articles\DTO\RegisterArticleDTO;
use App\Domain\Articles\DTO\GetArticleDTO;
use App\Domain\Articles\DTO\GetArticleListDTO;
use App\Domain\Articles\DTO\UpdateArticleDTO;
// Exceptions
use App\Exceptions\Articles\AlreadyExistIdException;

// VO
use App\Domain\Articles\ValueObject\ArticleDetail\ArticleTitle;
use App\Domain\Articles\ValueObject\ArticleDetail\ArticleDescription;

// Others
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Exceptions\Images\CouldNotSaveImageException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class DbArticlesInfrastructure implements ArticlesRepository
{
    private Article $article;
    private ArticleBlock $articleBlocks;
    private ArticleDetail $articleDetail;
    private ArticleOption $articleOption;
    private ArticleStatus $articleStatus;
    private ArticleTag $articleTag;
    private BlockImage $blockImage;
    private Tag $tag;
    private Status $status;
    private Option $option;
    private BlockTypes $blockTypes;

    public function __construct(
        Article $article,
        ArticleBlock $articleBlocks,
        ArticleDetail $articleDetail,
        ArticleOption $articleOption,
        ArticleStatus $articleStatus,
        ArticleTag $articleTag,
        BlockImage $blockImage,
        Tag $tag,
        Status $status,
        Option $option,
        BlockTypes $blockTypes

    ) {
        $this->article = $article;
        $this->articleBlocks = $articleBlocks;
        $this->articleDetail = $articleDetail;
        $this->articleOption = $articleOption;
        $this->articleStatus = $articleStatus;
        $this->articleTag = $articleTag;
        $this->blockImage = $blockImage;
        $this->tag = $tag;
        $this->status = $status;
        $this->option = $option;
        $this->blockTypes = $blockTypes;
    }

    #[\Override]
    public function registerArticles(RegisterArticleDTO $dto): ArticlesEntity
    {
        // todo: Userの存在チェックはここではなく、UseCaseの方でやるべき
        return DB::transaction(function () use ($dto) {

            $article = ArticlesEntity::createDraft(); // uuid は内部で生成
            $article->changeTitle($dto->detail['title'] ?? '');
            $article->changeNote($dto->detail['note'] ?? null);

            foreach (['tag1', 'tag2', 'tag3', 'tag4', 'tag5'] as $k) {
                if (!empty($dto->tags[$k])) {
                    $article->addTag(['content' => $dto->tags[$k]]);
                }
            }

            // ブロックは domain の BlockEntity/配列でセット
            $blocksDomain = [];
            foreach ($dto->blocks['baseBlocks'] ?? [] as $b) {
                $blocksDomain[] = [
                    'block_uuid' => $b['blockUuid'],
                    'content' => $b['content'] ?? null,
                    'order_from_parent_block' => $b['orderFromParentBlock'] ?? null,
                    'block_type' => $b['blockType'] ?? null,
                    'parent_block_uuid' => $b['parentBlockUuid'] ?? null,
                ];
            }
            $article->replaceBlocks($blocksDomain);

            // 2) 永続化: main article 取得（ID/uuid が必要）
            $savedMain = $this->article->create([
                'article_uuid' => (string) $article->getArticleUuid()
            ]);
            $article->setId($savedMain->id);

            // ユーザー存在チェック (外部キー違反を事前に防ぐ)
            $userUuid = $dto->detail['userInfo']['userUuid'] ?? null;
            if (!$userUuid) {
                throw new \InvalidArgumentException('userUuid is required in detail.userInfo');
            }
            if (!User::where('user_uuid', $userUuid)->exists()) {
                throw new NotFoundHttpException('user not found: ' . $userUuid);
            }

            // 3) 保存：detail (VO -> primitive)
            $titleForDb = $article->getDetail()?->getTitle() ? (string) $article->getDetail()->getTitle() : ($dto->detail['title'] ?? '');
            $noteForDb = $article->getDetail()?->getNote() ?? ($dto->detail['note'] ?? '');
            $descriptionForDb = $dto->detail['description'] ?? '';

            $detailAttrs = [
                'article_id' => $article->getId(),
                'user_uuid' => $userUuid,
                'title' => $titleForDb,
                'description' => $descriptionForDb,
                'note' => $noteForDb,
            ];
            $this->articleDetail->create($detailAttrs);

            // 4) 保存：blocks -> 保存後に Domain 用にマッピング
            $savedBlocks = [];
            foreach ($article->getBlocks()->getBlocks() as $b) {
                $typeId = $this->getBlockTypeIdByBlockType($b['block_type']);
                $saved = $this->articleBlocks->create([
                    'block_uuid' => $b['block_uuid'],
                    'article_id' => $article->getId(),
                    'block_type_id' => $typeId,
                    'content' => $b['content'],
                    'order_from_parent_block' => $b['order_from_parent_block'] ?? null,
                    'parent_block_uuid' => $b['parent_block_uuid'] ?? null,
                    'style' => $b['blockStyle'] ?? null,
                ]);
                $attrs = $saved->getAttributes();
                $savedBlocks[] = new BlockEntity(
                    $attrs['id'],
                    $attrs['block_uuid'],
                    $attrs['article_id'],
                    $attrs['parent_block_uuid'] ?? null,
                    $attrs['block_type_id'],
                    $attrs['content'],
                    $attrs['style'] ?? null,
                    null
                );
            }

            // 5) 保存：tags -> firstOrCreate し、安全に ID リストを作成
            $tagModels = [];
            foreach ($article->getTags()->getTags() as $t) {
                $tagModels[] = $this->tag->firstOrCreate(['content' => $t['content']]);
            }
            $tagIds = array_map(fn($m) => $m->id ?? null, $tagModels);
            $this->articleTag->create([
                'article_id' => $article->getId(),
                'tag_id_1' => $tagIds[0] ?? null,
                'tag_id_2' => $tagIds[1] ?? null,
                'tag_id_3' => $tagIds[2] ?? null,
                'tag_id_4' => $tagIds[3] ?? null,
                'tag_id_5' => $tagIds[4] ?? null,
            ]);

            // 6) options / statuses 同様に persist (省略)

            // 7) 再構築して返却（DBに保存された状態を確実に返す）
            $savedDetail = new ArticleDetailEntity(
                $detailAttrs['title'] !== '' ? ArticleTitle::fromString($detailAttrs['title']) : null,
                $detailAttrs['description'] !== '' ? ArticleDescription::fromString($detailAttrs['description']) : null,
                $detailAttrs['note'],
                null,
                /*statuses*/ null
            );
            $savedTagsEntity = new ArticleTagsEntity(
                $article->getId(),
                array_map(fn($m) => $m->toArray(), $tagModels)
            );
            $savedBlocksEntity = new ArticleBlockEntity(
                $article->getId(),
                /* block entities list */ $savedBlocks
            );

            return ArticlesEntity::reconstitute(
                $article->getId(),
                $savedMain->article_uuid,
                $savedDetail,
                $savedTagsEntity,
                $savedBlocksEntity,
                /* options */ []
            );
        });
    }

    // 未実装
    #[\Override]
    public function updateArticles(UpdateArticleDTO $dto): ArticlesEntity
    {

        $article = ArticlesEntity::createDraft($dto->articleUuid);

        DB::transaction(
            function () use ($dto) {

                $detail = $dto->detail;
                $status = $dto->status;
                $options = $dto->options;
                $tags = $dto->tags;
                $blocks = $dto->blocks;

                $this->updateArticleDetail($detail);


                if (!count($detail) === 0) {

                }

                if (!empty($dto->status)) {

                }

                if (!empty($dto->options)) {

                }

                if (!empty($dto->tags)) {

                }

                if (!empty($dto->blocks)) {

                }

                return new ArticlesEntity();
            }
        );

        return new ArticlesEntity();
    }

    // 未実装
    #[\Override]
    public function getArticlesList(GetArticleListDTO $dto): array
    {
        $articlesDetails = $this->articleDetail
            ->where('user_uuid', $dto->userId)
            ->orderBy('created_at', 'desc')
            ->paginate($dto->perPage);

        return $articlesDetails;
    }

    // 未実装
    #[\Override]
    public function getArticles(GetArticleDTO $dto): ArticlesEntity
    {
        try {
            $userName = $dto->userName;
            $articleId = $dto->articleId;

            // $userNameが存在しない場合にはエラーを返す。

            if (!$articleId) {
                throw new NotFoundHttpException('articleIdが不正です。');
            }

            $article = $this->article->where('id', $articleId)->first();

            if (!$article) {
                throw new NotFoundHttpException('記事が見つかりません。');
            }

            $articleAttributes = $article->getAttributes();
            $blocks = $this->articleBlocks->where('article_id', $articleId)->get();

            // ここはきちんとページの要素を組み立てる
            return new ArticlesEntity(
                $articleAttributes['id'],
                null, // ユーザーIDは記事の作成者のID
                null,
                null,
                null,
            );


        } catch (NotFoundHttpException $e) {
            throw new NotFoundHttpException($e->getMessage());
        }
    }

    // 実装修正したいかも
    // DBに保存後にそのままURLを返すのではなく、スラッグを返して、そのスラッグをもとに後で記事情報と紐づける形にしたいかも
    #[\Override]
    public function imageSave(Request $request): array
    {
        // この関数は最終的にはS3に置き換えたいけど、
        // それは自分がRust極めてRustで完璧にバックエンド描けるようになった時に予定しているリプレイスの時のために取っておく🍊

        try {

            // このpre_id付きのディレクトリは、画像の保存先を一時的に指定するためのもの
            // これが残ってる場合はバッチで削除するようにしたいね
            $preNewDirectoryName = 'pre-' . now()->format('Ymd-His') . '-' . Str::uuid();
            $newDirectory = public_path('articles/' . $preNewDirectoryName);
            $saveDestinationList = [];
            $host = request()->getSchemeAndHttpHost();

            if (!file_exists($newDirectory)) {
                mkdir($newDirectory, 0777, true);
            }

            $files = $request->file('file');
            $topImage = $request->file('topImage');

            // 単一ファイルの場合は配列に変換
            if (!is_array($files)) {
                $files = [$files];
            }

            // リクエスト内にトップイメージが存在する場合は、配列の先頭に追加
            // トップイメージは特別に扱いたい
            if ($topImage) {
                array_unshift($files, $topImage);
            }

            // webp化 + 保存処理
            // 画像の保存先は、/topImage または /articleImagesだけやけど、後々、記事を作成したユーザーのアイコンのリンクも追加するかも
            $imageManager = new ImageManager(new Driver());

            foreach ($files as $index => $image) {
                $blockUuid = $request->input("file.{$index}.blockUuid") ?? null;
                if (!$blockUuid) {
                    // ここでエラーをスローしたい
                }
                $imageName = $request->input("file.{$index}.imageName") ?? '';
                $altText = $request->input("file.{$index}.altText") ?? '';

                $img = $imageManager->read($image->getRealPath());

                if ($topImage && $index === 0) {
                    $targetDir = 'articles/' . $preNewDirectoryName . '/topImage';
                    $saveDir = public_path($targetDir);
                    if (!file_exists($saveDir)) {
                        mkdir($saveDir, 0777, true);
                    }
                    $fileName = 'topImage.webp';
                } else {
                    $targetDir = 'articles/' . $preNewDirectoryName . '/articleImages';
                    $saveDir = public_path($targetDir);
                    if (!file_exists($saveDir)) {
                        mkdir($saveDir, 0777, true);
                    }
                    $fileName = 'articleImage' . $index . '.webp';
                }

                $img->toWebp(90)->save($saveDir . '/' . $fileName);

                $savedImage = $this->blockImage->create([
                    'block_uuid' => $blockUuid,
                    'image_url' => $host . '/' . $targetDir . '/' . $fileName,
                    'image_name' => $imageName,
                    'alt_text' => $altText,
                ]);

                $imageUrl = new ImageEntity(
                    $savedImage->id,
                    $savedImage->block_uuid,
                    $savedImage->image_url,
                    $savedImage->image_name,
                    $savedImage->alt_text,
                );
                $saveDestinationList[$index] = $imageUrl;
            }

            return $saveDestinationList;

        } catch (CouldNotSaveImageException $e) {
            throw new CouldNotSaveImageException($e->getMessage());
        }
    }

    #[\Override]
    public function getInitProject(Request $request): ArticlesEntity
    {
        try {

            $userId = $request->query('userId');

            if (!$userId) {
                throw new NotFoundHttpException('userIdが不正です。');
            }
            // 将来的にはuserの情報から撮ってきたデータを返すようにする
            // でもまだユーザーの機能は実装していない(し、使うのが自分だけやから特に必要性も感じてない)ので、後回しにする🍣

            // ステータスをリスト化する
            $statuses = $this->status->all();
            $statusEntities = [];
            foreach ($statuses as $status) {
                $statusAttributes = $status->getAttributes();
                $statusEntities[] = new StatusEntity(
                    $statusAttributes['id'],
                    $statusAttributes['status_name'],
                    $statusAttributes['description']
                );
            }

            // オプションをリスト化する
            $options = $this->option->all();
            $optionEntities = [];
            foreach ($options as $option) {
                $optionAttributes = $option->getAttributes();
                $optionEntities[] = new OptionEntity(
                    $optionAttributes['id'],
                    $optionAttributes['option_name'],
                    $optionAttributes['description']
                );
            }

            // リクエスト内容
            return new ArticlesEntity(
                null,
                null,
                new ArticleDetailEntity(
                    null,
                    null,
                    null,
                    new ImageEntity(
                        null,
                        "",
                        "",
                        ""
                    ),
                    $statusEntities
                ),
                new ArticleTagsEntity(
                    null,
                    []
                ),
                new ArticleBlockEntity(
                    null,
                    []
                ),
                $optionEntities
            );
        } catch (NotFoundHttpException $e) {
            throw new NotFoundHttpException($e->getMessage());
        }
    }

    // 以下は便利に使えるメソッド

    private function registerMainArticle(): array
    {
        // 記事の登録
        $savedArticles = $this->article->create(
            [
                'article_uuid' => (string) Str::uuid()
            ]
        );

        return $savedArticles->getAttributes();
    }

    private function getBlockTypeIdByBlockType(string $blockType): int
    {
        $blockTypeRecord = $this->blockTypes->where('type_name', $blockType)->first();

        if (!$blockTypeRecord) {
            throw new NotFoundHttpException('ブロックタイプが見つかりません。');
        }

        return $blockTypeRecord->id;
    }

    private function registerBlockArticle($blocks, $id): array
    {

        try {
            $savedBlocks = [];

            $baseBlocks = $blocks['baseBlocks'] ?? [];

            // 管理のためにキーを追加する
            $imageBlocks = $this->addKeyBlock($blocks['images'] ?? []);
            $linkBlocks = $this->addKeyBlock($blocks['links'] ?? []);
            $codeBlocks = $this->addKeyBlock($blocks['code'] ?? []);

            // TODO: $block['parentBlockUuid']がnullのものから登録していくように修正したい
            // 上の階層から登録していくようにしたい
            $savedBlocks[] = DB::transaction(function () use ($baseBlocks, $imageBlocks, $linkBlocks, $codeBlocks, $id, ) {
                foreach ($baseBlocks as $block) {
                    $etcInfo = [];

                    $savedBlock = $this->articleBlocks->create([
                        'block_uuid' => $block['blockUuid'], // このuuidはフロントエンド側で生成したものを使用
                        'article_id' => $id,
                        'block_type_id' => $this->getBlockTypeIdByBlockType($block['blockType']),
                        'content' => $block['content'],
                        // 以下は各タグごとにオプションで必要になる項目
                        // これオプションとして別テーブルに切り出すべきかも？？？
                        'parent_block_uuid' => null,
                        'order_from_parent_block' => $block['orderFromParentBlock'] ?? null,
                        'style' => $block['blockStyle'] ?? null,
                    ]);

                    // 親ブロックのUUIDを設定
                    // $savedBlock->parent_block_uuid = $block['parentBlockUuid'] ?? null;
                    // $savedBlock->save();

                    $savedBlockAttributes = $savedBlock->getAttributes();

                    // 各ブロックのタイプに応じて、etc情報を設定
                    switch ($block['blockType']) {
                        case 'img':

                            // 画像ブロックの場合は別のAPIで画像の情報を取得しているので、以前登録した情報を取得するだけでいい
                            $imageBlock = $this->findImageBlockByUuid($block['blockUuid']);

                            // 画像ブロックが見つからない場合は、登録する
                            if (!$imageBlock) {
                                $imageBlock = $this->registerImageBlock($imageBlocks[$block['blockUuid']] ?? []);
                            }

                            $etcInfo = $imageBlock ? $imageBlock->getAttributes() : null;

                            break;
                        // case 'link':

                        //     $etcInfo = $this->registerLinkBlock(
                        //         $block,
                        //         $savedBlockAttributes['block_uuid']
                        //     );

                        //     break;
                        // case 'code':

                        //     break;
                    }

                    return new BlockEntity(
                        $savedBlockAttributes['id'],
                        $savedBlockAttributes['block_uuid'],
                        $savedBlockAttributes['article_id'],
                        $savedBlockAttributes['parent_block_uuid'] ?? null,
                        $savedBlockAttributes['block_type_id'],
                        $savedBlockAttributes['content'],
                        $savedBlockAttributes['style'] ?? null,
                        $etcInfo
                    );
                }
            });

            return $savedBlocks;
        } catch (\Exception $e) {
            throw new AlreadyExistIdException('ブロック登録にあたって、既に存在するIDが使用されました。IDは一意なものを使用してください。', [$e->getMessage()]);
        }
    }
    private function registerDetailArticle($detail, $id): array
    {
        $userData = $detail['userInfo'];

        $savedDetail = $this->articleDetail->create([
            'article_id' => $id,
            'user_uuid' => $userData['userUuid'],
            'title' => $detail['title'],
            'description' => $detail['description'] ?? '',
            'note' => $detail['note'] ?? '',
        ]);

        return $savedDetail->getAttributes();
    }
    private function registerStatusArticle($status, $id): array
    {

        $savedStatuses = [];

        foreach ($status as $st) {
            $savedStatus = $this->articleStatus->create([
                'article_id' => $id,
                'status_id' => $st['statusId'],
                'status_value' => $st['statusValue'],
            ]);

            $status = $savedStatus->getAttributes();

            $savedStatuses[] = new ArticleStatusEntity(
                $status['article_id'],
                $status['status_id'],
                $status['status_value']
            );
        }

        return $savedStatuses;
    }

    private function registerTagsArticle($tag, $id): ArticleTagsEntity
    {

        $tagsFromDb = [];

        if (empty($tag['tag1'])) {
            throw new \InvalidArgumentException('タグ1は必須です。');
        }

        // 1個目のタグは必須なので、必ず登録する
        $tagsFromDb[] = $this->tag->firstOrCreate(['content' => $tag['tag1']]);

        if (!empty($tag['tag2'])) {
            $tagsFromDb[] = $this->tag->firstOrCreate(['content' => $tag['tag2']]);
        }
        if (!empty($tag['tag3'])) {
            $tagsFromDb[] = $this->tag->firstOrCreate(['content' => $tag['tag3']]);
        }
        if (!empty($tag['tag4'])) {
            $tagsFromDb[] = $this->tag->firstOrCreate(['content' => $tag['tag4']]);
        }
        if (!empty($tag['tag5'])) {
            $tagsFromDb[] = $this->tag->firstOrCreate(['content' => $tag['tag5']]);
        }

        $tag1 = $tagsFromDb[0]->getAttributes();
        $tag2 = !empty($tagsFromDb[1]) ? $tagsFromDb[1]->getAttributes() : null;
        $tag3 = !empty($tagsFromDb[2]) ? $tagsFromDb[2]->getAttributes() : null;
        $tag4 = !empty($tagsFromDb[3]) ? $tagsFromDb[3]->getAttributes() : null;
        $tag5 = !empty($tagsFromDb[4]) ? $tagsFromDb[4]->getAttributes() : null;

        $this->articleTag->create([
            'article_id' => $id,
            'tag_id_1' => $tag1['id'],
            'tag_id_2' => $tag2 ? $tag2['id'] : null,
            'tag_id_3' => $tag3 ? $tag3['id'] : null,
            'tag_id_4' => $tag4 ? $tag4['id'] : null,
            'tag_id_5' => $tag5 ? $tag5['id'] : null,
        ]);

        return new ArticleTagsEntity(
            $id,
            [
                'tag1' => [
                    'id' => $tag1['id'],
                    'content' => $tag1['content'],
                ],
                'tag2' => $tag2 ? [
                    'id' => $tag2['id'],
                    'content' => $tag2['content'],
                ] : null,
                'tag3' => $tag3 ? [
                    'id' => $tag3['id'],
                    'content' => $tag3['content'],
                ] : null,
                'tag4' => $tag4 ? [
                    'id' => $tag4['id'],
                    'content' => $tag4['content'],
                ] : null,
                'tag5' => $tag5 ? [
                    'id' => $tag5['id'],
                    'content' => $tag5['content'],
                ] : null,
            ]
        );
    }

    private function registerOptions(array $option, int $id): array
    {

        $savedOptions = [];

        foreach ($option as $op) {
            $savedOption = $this->articleOption->create([
                'article_id' => $id,
                'option_id' => $op['optionId'],
                'option_value' => $op['optionValue'],
            ]);

            $savedOptions[] = new ArticleOptionsEntity(
                $savedOption->getAttributes()['article_id'],
                $savedOption->getAttributes()['option_id'],
                $savedOption->getAttributes()['option_value']
            );
        }
        return $savedOptions;
    }

    private function findImageBlockByUuid(string $blockUuid): ?BlockImage
    {
        try {
            return $this->blockImage->where('block_uuid', $blockUuid)->first();
        } catch (\Exception $e) {
            throw new \RuntimeException('UUIDを使用したブロックの取得に失敗しました。', 0, $e);
        }
    }

    private function registerImageBlock(array $block): BlockImage
    {

        try {

            $savedBlockImage = $this->blockImage->create([
                'block_uuid' => $block['blockUuid'] ?? null,
                'image_name' => $block['imageName'] ?? null,
                'image_url' => $block['imageUrl'] ?? null,
                'alt_text' => $block['altText'] ?? null,
            ]);

            return $savedBlockImage;

        } catch (\Exception $e) {
            throw new \RuntimeException('画像ブロックの付帯情報の登録に失敗しました。', 0, $e);
        }
    }

    private function addKeyBlock(array $block): array
    {
        return array_reduce($block, function ($result, $block) {
            $result[$block['blockUuid']] = $block;
            return $result;
        }, []);
    }

    private function getArticleProjectByArticleUuid(string $articleUuid): array
    {
        try {

            $articleDetail = $this->articleDetail->where('article_uuid', $articleUuid)->first();

            if (!$articleDetail) {
                throw new NotFoundHttpException('記事の詳細情報が見つかりません。');
            }

            $article = $this->article->where('id', $articleDetail->article_id)->first();

            if (!$article) {
                throw new NotFoundHttpException('記事が見つかりません。');
            }

            $articleStatus = $this->articleStatus->where('article_id', $article->id)->get();

            if (!$articleStatus) {
                throw new NotFoundHttpException('記事のステータスが見つかりません。');
            }

            $articleTags = $this->articleTag->where('article_id', $article->id)->get();

            if (!$articleTags) {
                throw new NotFoundHttpException('記事のタグが見つかりません。');
            }

            $articleOptions = $this->articleOption->where('article_id', $article->id)->get();

            if (!$articleOptions) {
                throw new NotFoundHttpException('記事のオプションが見つかりません。');
            }

            $articleBlocks = $this->articleBlocks->where('article_id', $article->id)->get();

            if (!$articleBlocks) {
                throw new NotFoundHttpException('記事のブロックが見つかりません。');
            }

            return [
                'articleDetail' => $articleDetail->toArray(),
                'article' => $article->toArray(),
                'articleStatus' => $articleStatus->toArray(),
                'articleTags' => $articleTags->toArray(),
                'articleOptions' => $articleOptions->toArray(),
                'articleBlocks' => $articleBlocks->toArray(),
            ];

        } catch (\Exception $e) {
            throw new NotFoundHttpException('記事が見つかりません。');
        }
    }

    // update

    private function updateArticleDetail(array $data): array
    {
        return [];
    }
}
