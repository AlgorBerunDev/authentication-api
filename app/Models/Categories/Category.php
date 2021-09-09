<?php

namespace App\Models\Categories;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Categories\CategoryTranslations;

class Category extends Model
{
    use HasFactory;
    use \Astrotomic\Translatable\Translatable;

    protected $fillable = [
        'parent_id',
        'name',
        'url',
        'slug',
        'published',
        'published_at',
        'icon',
        'background_image',
        'avatar_image'
    ];
    public $translatedAttributes = ['title', 'description'];
    public function childs() {
        return $this->hasMany(Category::class, 'parent_id', 'id');
    }
}
