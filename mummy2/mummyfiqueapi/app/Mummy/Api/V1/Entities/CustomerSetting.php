<?php namespace App\Mummy\Api\V1\Entities;

use Illuminate\Database\Eloquent\Model;
class CustomerSetting extends Model
{
    protected $table = 'mm__customer_settings';
    protected $fillable = [
    	'id',
        'user_id',
        'key',
        'value',
        'created_at',
        'updated_at',
        'status',
    ];
   
}
