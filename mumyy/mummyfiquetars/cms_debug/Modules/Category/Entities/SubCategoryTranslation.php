<?php namespace Modules\Category\Entities;

use Illuminate\Database\Eloquent\Model;

class SubCategoryTranslation extends Model
{
    public $timestamps = false;
    protected $fillable = [];
    protected $table = 'category__subcategory_translations';
}
