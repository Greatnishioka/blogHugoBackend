<?php
use PHPUnit\Framework\TestCase;
use App\Domain\Articles\Entity\ArticlesEntity;
use App\Domain\Articles\ValueObject\ArticleUuid;

class ArticlesEntityUuidTest extends TestCase
{

    // このプロジェクトにおける最初のテストでありながら、
    // わたくし、西岡礼人の書く人生初めての記念すべきテストコードである🍣
    // 品質管理頑張りますよん

    public function test_createDraft_and_getArticleUuid()
    {
        $e = ArticlesEntity::createDraft();
        $this->assertNotNull($e->getArticleUuid());
        $this->assertIsString($e->getArticleUuid());
    }

    public function test_reconstitute_with_string_uuid()
    {
        $uuid = (string) ArticleUuid::generate();
        $e = ArticlesEntity::reconstitute(null, $uuid, null, null, null, null);
        $this->assertSame($uuid, $e->getArticleUuid());
    }

    public function test_setArticleUuid_allows_null()
    {
        $e = ArticlesEntity::createDraft();
        $e->setArticleUuid(null);
        $this->assertNull($e->getArticleUuid());
    }
}