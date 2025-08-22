<?php
namespace App\Models\Articles;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticleBlock extends Model
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
        'order_from_parent_block',
        'block_type_id',
        'content',
        'style'
    ];
}