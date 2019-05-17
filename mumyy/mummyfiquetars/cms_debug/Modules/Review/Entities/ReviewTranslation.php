<?php namespace Modules\Review\Entities;

use Illuminate\Database\Eloquent\Model;

class ReviewTranslation extends Model
{
    public $timestamps = false;
    protected $fillable = [];
    protected $table = 'review__review_translations';
}
