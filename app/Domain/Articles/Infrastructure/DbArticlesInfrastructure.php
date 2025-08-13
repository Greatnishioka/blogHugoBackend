<?php

namespace App\Domain\Articles\Infrastructure;
use Illuminate\Http\Request;
// Models
use App\Models\Articles\Article;
use App\Models\Articles\ArticleBlock;
use App\Models\Articles\ArticleDetail;
use App\Models\Articles\ArticleOption;
use App\Models\Articles\ArticleStatus;
use App\Models\Articles\ArticleTag;
use App\Models\Articles\Blocks\BlockImage;
use App\Models\Status\Status;
use App\Models\Options\Option;
use App\Models\Images\Image;
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
// Others
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Exceptions\Images\CouldNotSaveImageException;
use Illuminate\Support\Str;

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
        Option $option

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
    }

    #[\Override]
    public function registerArticles(RegisterArticleDTO $dto): ArticlesEntity
    {
        try {

            // 記事の大枠の作成
            // ここで作成した記事のIDを元に、他の情報を紐づけていく
            $savedArticles = $this->registerMainArticle();

            // 記事のブロックの作成
            $savedBlocks = $this->registerBlockArticle(
                $dto->blocks,
                $savedArticles['id']
            );

            // 記事の詳細の作成
            $savedDetail = $this->registerDetailArticle(
                $dto->detail,
                $savedArticles['id']
            );

            // 記事のステータスの作成
            $savedStatus = $this->registerStatusArticle(
                $dto->status,
                $savedArticles['id']
            );

            // 記事のタグの作成
            $savedTags = $this->registerTagsArticle(
                $dto->tags,
                $savedArticles['id']
            );

            // 記事のオプションの作成(公開・非公開など)
            $savedOptions = $this->registerOptions(
                $dto->options,
                $savedArticles['id']
            );

            return new ArticlesEntity(
                $savedArticles['id'],
                new ArticleDetailEntity(
                    $savedDetail['article_uuid'],
                    $savedDetail['title'],
                    $savedDetail['note'],
                    null,
                    $savedStatus
                ),
                new ArticleTagsEntity(
                    $savedArticles['id'],
                    $savedTags
                ),
                new ArticleBlockEntity(
                    $savedArticles['id'],
                    $savedBlocks

                ),
                $savedOptions,
            );
        } catch (NotFoundHttpException $e) {
            throw new NotFoundHttpException($e->getMessage());
        }
    }

    // 未実装
    #[\Override]
    public function updateArticles(Request $request): ArticlesEntity
    {
        return new ArticlesEntity(
            $request->input('id'),
            new ArticleDetailEntity(
                $request->input('detail.article_uuid'),
                $request->input('detail.title'),
                $request->input('detail.author'),
                $request->input('detail.author_id'),
                $request->input('status'),
            ),
            new ArticleTagsEntity(
                $request->input('id'),
                $request->input('tags')
            ),
            new ArticleBlockEntity(
                $request->input('id'),
                $request->input('blocks')
            ),
            $request->input('options')
        );
    }

    // 未実装
    #[\Override]
    public function getArticlesList(GetArticleDTO $dto): array
    {
        $articlesDetails = $this->articleDetail
            ->where('user_uuid', $dto->userId)
            ->orderBy('created_at', 'desc')
            ->paginate($dto->perPage);

        return $articlesDetails;
    }

    // 未実装
    #[\Override]
    public function getArticles(Request $request): ArticlesEntity
    {
        try {
            $userName = $request->query('userName');
            $articleId = $request->query('articleId');

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
                $articleAttributes['user_id'], // ユーザーIDは記事の作成者のID
                $articleAttributes['article_id'],
                null,
                null,
            );


        } catch (NotFoundHttpException $e) {
            throw new NotFoundHttpException($e->getMessage());
        }
    }

    #[\Override]
    public function imageSave(Request $request): array
    {
        // この関数は最終的にはS3に置き換えたいけど、
        // それは自分がRust極めてRustで完璧にバックエンド描けるようになった時に予定しているリプレイスの時のために取っておく🍊

        try {

            // このpre_id付きのディレクトリは、画像の保存先を一時的に指定するためのもの
            // これが残ってる場合はバッチで削除するようにしたいね
            $preNewDirectoryName = now()->format('Ymd-His') . '_pre-id-' . Str::uuid();
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
                new ArticleDetailEntity(
                    null,
                    null,
                    "tester", // 将来的にはユーザーの情報を取得して表示する
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

    public function registerMainArticle(): array
    {
        // 記事の登録
        $savedArticles = $this->article->create();

        return $savedArticles->getAttributes();
    }

    public function registerBlockArticle($blocks, $id): array
    {

        $savedBlocks = [];

        foreach ($blocks as $block) {
            $savedEtcInfo = [];

            $savedBlock = $this->articleBlocks->create([
                'block_uuid' => $block['blockUuid'], // このuuidはフロントエンド側で生成したものを使用
                'article_id' => $id,
                'block_type' => $block['blockType'],
                'content' => $block['content'],
                // 以下は各タグごとにオプションで必要になる項目
                'parent_block_uuid' => $block['parentBlockUuid'] ?? null,
                'order_from_parent_block' => $block['orderFromParentBlock'] ?? null,
                'style' => $block['blockStyle'] ?? null,
            ]);

            $savedBlockAttributes = $savedBlock->getAttributes();

            // 各ブロックのタイプに応じて、etc情報を設定
            switch ($block['blockType']) {
                case 'img':

                    // block_uuidを使用して、画像の情報を保存
                    $savedEtcInfo = $this->registerImageBlock(
                        $block,
                        $savedBlockAttributes['block_uuid']
                    );

                    break;
                // case 'link':

                //     $savedEtcInfo = $this->registerLinkBlock(
                //         $block,
                //         $savedBlockAttributes['block_uuid']
                //     );

                //     break;
                // case 'code':

                //     break;
            }

            $savedBlocks[] = new BlockEntity(
                $savedBlockAttributes['id'],
                $savedBlockAttributes['block_uuid'],
                $savedBlockAttributes['article_id'],
                $savedBlockAttributes['parent_block_uuid'] ?? null,
                $savedBlockAttributes['block_type'],
                $savedBlockAttributes['content'],
                $savedBlockAttributes['style'] ?? null,
                $savedEtcInfo
            );
        }

        return $savedBlocks;
    }
    private function registerDetailArticle($detail, $id): array
    {

        $savedDetail = $this->articleDetail->create([
            'article_id' => $id,
            'user_uuid' => $detail['userUuid'],
            'article_uuid' => (string) Str::uuid(),
            'title' => $detail['title'],
            'note' => $detail['note'], // これ用途どうする？
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

    private function registerTagsArticle($tag, $id): array
    {

        $tags = [];
        if (!empty($tag['tag1'])) {
            $tags[] = $this->tag->firstOrCreate(['content' => $tag['tag1']]);
        }
        if (!empty($tag['tag2'])) {
            $tags[] = $this->tag->firstOrCreate(['content' => $tag['tag2']]);
        }
        if (!empty($tag['tag3'])) {
            $tags[] = $this->tag->firstOrCreate(['content' => $tag['tag3']]);
        }
        if (!empty($tag['tag4'])) {
            $tags[] = $this->tag->firstOrCreate(['content' => $tag['tag4']]);
        }
        if (!empty($tag['tag5'])) {
            $tags[] = $this->tag->firstOrCreate(['content' => $tag['tag5']]);
        }

        return array_map(fn($tag) => new ArticleTagsEntity($id, $tag->getAttributes()), $tags);
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

    private function registerImageBlock(array $block, string $uuid): array
    {
        $savedBlockImage = $this->blockImage->create([
            'block_uuid' => $uuid,
            'image_name' => $block['imageName'] ?? null,
            'image_url' => $block['imageUrl'] ?? null,
            'alt_text' => $block['altText'] ?? null,
        ]);

        return $savedBlockImage->toArray();
    }
}
