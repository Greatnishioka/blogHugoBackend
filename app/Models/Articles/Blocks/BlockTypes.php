<?php
namespace App\Models\Articles\Blocks;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlockTypes extends Model
{
    use HasFactory;

    protected $table = 'block_types';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'type_name',
        'description',
        'is_available'
    ];
}