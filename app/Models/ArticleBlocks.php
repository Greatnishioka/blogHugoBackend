<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticleBlocks extends Model
{
    use HasFactory;

    protected $table = 'articles_blocks';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'block_uuid',
        'article_id',
        'parent_block_uuid',
        'block_type',
        'content',
        'style',
        'url',
        'language',
    ];
}
