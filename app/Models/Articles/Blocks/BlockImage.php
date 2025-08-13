<?php
namespace App\Models\Articles\Blocks;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlockImage extends Model
{
    use HasFactory;

    protected $table = 'block_image';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'block_uuid',
        'image_url',
        'image_name',
        'alt_text',
    ];
}
