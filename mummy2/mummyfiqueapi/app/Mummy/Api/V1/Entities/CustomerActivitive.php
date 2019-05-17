<?php namespace App\Mummy\Api\V1\Entities;

use Illuminate\Database\Eloquent\Model;
class CustomerActivitive extends Model
{
    protected $table = 'mm__user_activities';
    protected $fillable = [
    	'id',
        'user_id',
        'vendor_id',
        'portfolio_id',
        'activity',
        'created_at',
        'updated_at',
    ];
   
}