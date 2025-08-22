<?php

namespace App\Models\User\Occupations;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Occupations extends Model
{
    use HasFactory;

    protected $table = 'occupations';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'occupation_name',
        'occupation_name_ja',
        'description',
        'is_available',
    ];
}