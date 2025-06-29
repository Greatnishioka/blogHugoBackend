<?php
namespace App\Models\Articles;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticleOption extends Model
{
    use HasFactory;

    protected $table = 'articles_options';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    
    protected $fillable = [
        'article_id',
        'is_private',
    ];
}
