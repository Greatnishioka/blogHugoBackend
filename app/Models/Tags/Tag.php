<?php
namespace App\Models\Tags;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    protected $table = 'tags';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    
    protected $fillable = [
        'article_id',
        'content',
        'relatedTags',
    ];
}
