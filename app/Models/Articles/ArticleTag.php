<?php
namespace App\Models\Articles;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticleTag extends Model
{
    use HasFactory;

    protected $table = 'articles_tags';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    
    protected $fillable = [
        'article_id',
        'tag_id_1',
        'tag_id_2',
        'tag_id_3',
        'tag_id_4',
        'tag_id_5',
    ];
}
