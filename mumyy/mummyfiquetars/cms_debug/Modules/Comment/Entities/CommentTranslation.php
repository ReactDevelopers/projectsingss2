<?php namespace Modules\Comment\Entities;

use Illuminate\Database\Eloquent\Model;

class CommentTranslation extends Model
{
    public $timestamps = false;
    protected $fillable = [];
    protected $table = 'comment__comment_translations';
}
