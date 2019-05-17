<?php

namespace App\Mummy\Api\V1\Entities;

use Illuminate\Database\Eloquent\Model;

class SendMessage extends Model
{
     protected $table = 'mm__send_message';
    protected $fillable = ['id','sender_id','receiver_id','subject','message','status','is_read','is_customer_read','is_vendor_read','is_deleted','is_customer_deleted','is_vendor_deleted','created_at','updated_at'];

}
