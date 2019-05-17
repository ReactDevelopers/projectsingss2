<?php

namespace App\Mummy\Api\V1\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReviewReply extends Model
{
    use SoftDeletes;

    protected $fillable = ['id','review_id','sender_id','receiver_id','reply_content','status','is_deleted','created_at','updated_at'];

    protected $table = 'mm__user_review_reply';

    const DELETED_AT = 'is_deleted';

}
