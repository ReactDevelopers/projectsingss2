<?php namespace App\Mummy\Api\V1\Entities;

use Illuminate\Database\Eloquent\Model;
class CustomerActivation extends Model
{
    protected $table = 'customer_activation';
    protected $primaryKey = 'customer_id';
    public $timestamps = false;
    protected $fillable = [
    	'customer_id',
        'token',
        'created_at',
    ];
   
}