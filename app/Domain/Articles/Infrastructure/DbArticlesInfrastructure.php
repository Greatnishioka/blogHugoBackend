<?php

namespace App\Domain\Articles\Infrastructure;
use Illuminate\Http\Request;
use App\Models\Article;
use App\Models\ArticleBlocks;
use App\Domain\Articles\Entity\ArticlesEntity;
use App\Domain\Articles\Entity\ArticlesBlockEntity;
use App\Domain\Articles\Repository\ArticlesRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DbArticlesInfrastructure implements ArticlesRepository
{
    private Article $article;
    private ArticleBlocks $articleBlocks;

    public function __construct(Article $article, ArticleBlocks $articleBlocks)
    {
        $this->article = $article;
        $this->articleBlocks = $articleBlocks;
    }

    #[\Override]
    public function registerArticles(Request $request):ArticlesEntity
    {
        try{

            $savedArticles = $this->article->create([
                'title' => $request->input('title'),
                'author' => $request->input('author'),
                'author_id' => $request->input('authorId'),
                'view_count' => 0,
            ]);

            $savedArticlesAttributes = $savedArticles->getAttributes();
            $blocks = $request->input('blocks');
            $savedBlocks = [];

            foreach ($blocks as $block) {
                $savedBlock = $this->articleBlocks->create([
                    'block_uuid' => $block['blockUuid'],
                    'article_id' => $savedArticlesAttributes['id'],
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

                $savedBlocks[] = new ArticlesBlockEntity(
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

            return new ArticlesEntity(
                $savedArticlesAttributes['id'],
                $savedArticlesAttributes['title'],
                $savedArticlesAttributes['author'],
                $savedArticlesAttributes['author_id'],
                $savedArticlesAttributes['view_count'],
                $savedBlocks
            );
        }
        catch (NotFoundHttpException $e) {
            throw new NotFoundHttpException($e->getMessage());
        }
    }

    #[\Override]
    public function getArticles(Request $request):ArticlesEntity
    {
        try{
            $userName = $request->query('userName');
            $articleId = $request->query('articleId');

            // $userNameが存在しない場合にはエラーを返す。

            if(!$articleId){
                throw new NotFoundHttpException('articleIdが不正です。');
            }

            $article = $this->article->where('id', $articleId)->first();

            if(!$article){
                throw new NotFoundHttpException('記事が見つかりません。');
            }

            $articleAttributes = $article->getAttributes();
            $blocks = $this->articleBlocks->where('article_id', $articleId)->get();

            return new ArticlesEntity(
                $articleAttributes['id'],
                $articleAttributes['title'],
                $articleAttributes['author'],
                $articleAttributes['author_id'],
                $articleAttributes['view_count'],
                array_map(function ($block) {
                    $blockAttributes = $block->getAttributes();
                    return new ArticlesBlockEntity(
                        $blockAttributes['id'],
                        $blockAttributes['article_id'],
                        $blockAttributes['parent_block_uuid'] ?? null,
                        $blockAttributes['block_type'],
                        $blockAttributes['content'],
                        $blockAttributes['style'] ?? null,
                        $blockAttributes['url'] ?? null,
                        $blockAttributes['language'] ?? null
                    );
                }, $blocks->all())
            );

            
        }
        catch (NotFoundHttpException $e) {
            throw new NotFoundHttpException($e->getMessage());
        }
    }
    // public function getSearchUser(Request $request)
    // {

    // }
}
