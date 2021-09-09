<?php

namespace App\Models\Categories;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class CategoryTranslation extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'description',
        'info_url',
        'category_id',
        'locale'
    ];
    public $timestamps = false;
}
