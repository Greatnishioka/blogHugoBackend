<?php

namespace App\Domain\Articles\Infrastructure;
use Illuminate\Http\Request;
// Models
use App\Models\Articles\Article;
use App\Models\Articles\ArticleBlocks;
use App\Models\Articles\ArticleDetail;
use App\Models\Articles\ArticleOption;
use App\Models\Articles\ArticleStatus;
use App\Models\Articles\ArticleTag;
use App\Models\Status\Status;
use App\Models\Options\Option;
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
use App\Domain\Articles\Entity\Images\ImageUrlEntity;
// Repositories
use App\Domain\Articles\Repository\ArticlesRepository;
// Others
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Exceptions\Images\CouldNotSaveImageException;
use Illuminate\Support\Str;

class DbArticlesInfrastructure implements ArticlesRepository
{
    private Article $article;
    private ArticleBlocks $articleBlocks;
    private ArticleDetail $articleDetail;
    private ArticleOption $articleOption;
    private ArticleStatus $articleStatus;
    private ArticleTag $articleTag;
    private Tag $tag;
    private Status $status;
    private Option $option;

    public function __construct(
        Article $article,
        ArticleBlocks $articleBlocks,
        ArticleDetail $articleDetail,
        ArticleOption $articleOption,
        ArticleStatus $articleStatus,
        ArticleTag $articleTag,
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
        $this->tag = $tag;
        $this->status = $status;
        $this->option = $option;
    }

    #[\Override]
    public function registerArticles(Request $request): ArticlesEntity
    {
        try {

            // 記事の大枠の作成
            // ここで作成した記事のIDを元に、他の情報を紐づけていく
            $savedArticles = $this->registerMainArticle($request->input('userId'));

            // 記事のブロックの作成
            $savedBlocks = $this->registerBlockArticle(
                $request->input('blocks'),
                $savedArticles['id']
            );

            // 記事の詳細の作成
            $savedDetail = $this->registerDetailArticle(
                $request->input('detail'),
                $savedArticles['id']
            );

            // 記事のステータスの作成
            $savedStatus = $this->registerStatusArticle(
                $request->input('status'),
                $savedArticles['id']
            );

            // 記事のタグの作成
            $savedTags = $this->registerTagsArticle(
                $request->input('tags'),
                $savedArticles['id']
            );

            // 記事のオプションの作成(公開・非公開など)
            $savedOptions = $this->registerOptions(
                $request->input('options'),
                $savedArticles['id']
            );

            return new ArticlesEntity(
                $savedArticles['id'],
                $savedArticles['user_id'], // ユーザーIDは記事の作成者のID
                $savedArticles['article_id'], // このidは閲覧者が記事へのアクセスの時にurlに露出する用
                new ArticleDetailEntity(
                    $savedArticles['id'],
                    $savedDetail['title'],
                    $savedDetail['author'],
                    $savedDetail['author_id'],
                    null, // これちゃんとプログラムを組む
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
                new ArticleDetailEntity(
                    $articleAttributes['id'],
                    null, // $savedDetail['title'],
                    null, // $savedDetail['author'],
                    null, // $savedDetail['author_id'],
                    null, // null, // これちゃんとプログラムを組む
                    []
                ),
                new ArticleTagsEntity(
                    $articleAttributes['id'],
                    []
                ),
                new ArticleBlockEntity(
                    $articleAttributes['id'],
                    []

                ),
                [],
            );


        } catch (NotFoundHttpException $e) {
            throw new NotFoundHttpException($e->getMessage());
        }
    }

    #[\Override]
    public function imageSave(Request $request): ImagesEntity
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
                $img = $imageManager->read($image->getRealPath());

                if ($topImage && $index === 0) {
                    $saveDir = public_path('articles/' . $preNewDirectoryName . '/topImage');
                    if (!file_exists($saveDir)) {
                        mkdir($saveDir, 0777, true);
                    }
                    $fileName = 'topImage.webp';
                } else {
                    $saveDir = public_path('articles/' . $preNewDirectoryName . '/articleImages');
                    if (!file_exists($saveDir)) {
                        mkdir($saveDir, 0777, true);
                    }
                    $fileName = 'articleImage' . $index . '.webp';
                }

                $img->toWebp(90)->save($saveDir . '/' . $fileName);

                $imageUrl = new ImageUrlEntity(
                    null,
                    $host . '/' . $preNewDirectoryName . ($topImage && $index === 0 ? '/topImage' : '/articleImages') . '/' . $fileName,
                    null,
                    null,
                );
                $saveDestinationList[$index] = $imageUrl;
            }

            return new ImagesEntity($saveDestinationList);

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
                $userId, // ユーザーIDは記事の作成者のID
                null,
                new ArticleDetailEntity(
                    null,
                    null,
                    "tester", // 将来的にはユーザーの情報を取得して表示する
                    $userId,
                    new ImageUrlEntity(
                        "tester",
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

    public function registerMainArticle(int $userId): array
    {
        // 記事の登録
        $savedArticles = $this->article->create([
            'user_id' => $userId, // ユーザーIDは記事の作成者のID
            'article_id' => (string) Str::uuid(),
        ]);

        return $savedArticles->getAttributes();
    }

    public function registerBlockArticle($blocks, $id): array
    {

        $savedBlocks = [];

        foreach ($blocks as $block) {
            $savedBlock = $this->articleBlocks->create([
                'block_uuid' => $block['blockUuid'],
                'article_id' => $id,
                'block_type' => $block['blockType'],
                'content' => $block['content'],
                // 以下は各タグごとにオプションで必要になる項目
                'parent_block_uuid' => $block['parentBlockUuid'] ?? null,
                'order_from_parent_block' => $block['orderFromParentBlock'] ?? null,
                'style' => $block['blockStyle'] ?? null,
                'url' => $block['blockUrl'] ?? null,
                'language' => $block['blockLanguage'] ?? null,
            ]);

            $savedBlockAttributes = $savedBlock->getAttributes();

            $savedBlocks[] = new BlockEntity(
                $savedBlockAttributes['id'],
                $savedBlockAttributes['article_id'],
                $savedBlockAttributes['parent_block_uuid'] ?? null,
                $savedBlockAttributes['block_type'],
                $savedBlockAttributes['content'],
                $savedBlockAttributes['style'] ?? null,
                $savedBlockAttributes['url'] ?? null,
                $savedBlockAttributes['language'] ?? null
            );
        }

        return $savedBlocks;
    }
    private function registerDetailArticle($detail, $id): array
    {

        $savedDetail = $this->articleDetail->create([
            'article_id' => $id,
            'title' => $detail['title'],
            'author' => $detail['author'],
            'author_id' => $detail['authorId'],
        ]);

        return $savedDetail->getAttributes();
    }
    private function registerStatusArticle($status, $id): array
    {

        $savedStatuses = [];

        foreach ($status as $st) {
            $savedStatus = $this->articleStatus->create([
                'article_id' => $id,
                'option_id' => $st['statusId'],
                'option_value' => $st['statusValue'],
            ]);

            $status = $savedStatus->getAttributes();

            $savedStatuses[] = new ArticleStatusEntity(
                $status['article_id'],
                $status['option_id'],
                $status['option_value']
            );
        }

        return $savedStatuses;
    }

    private function registerTagsArticle($tag, $id): array
    {

        $tag1 = $this->tag->firstOrCreate(['content' => $tag['tag1']]);
        $tag2 = $this->tag->firstOrCreate(['content' => $tag['tag2']]);
        $tag3 = $this->tag->firstOrCreate(['content' => $tag['tag3']]);
        $tag4 = $this->tag->firstOrCreate(['content' => $tag['tag4']]);
        $tag5 = $this->tag->firstOrCreate(['content' => $tag['tag5']]);

        return [
            new ArticleTagsEntity($id, $tag1->getAttributes()),
            new ArticleTagsEntity($id, $tag2->getAttributes()),
            new ArticleTagsEntity($id, $tag3->getAttributes()),
            new ArticleTagsEntity($id, $tag4->getAttributes()),
            new ArticleTagsEntity($id, $tag5->getAttributes()),
        ];
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
}
