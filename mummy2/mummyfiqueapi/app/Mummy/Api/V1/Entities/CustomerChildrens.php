<?php namespace App\Mummy\Api\V1\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class CustomerChildrens extends Model
{
    use SoftDeletes;
    protected $table = 'mm__customer_childrens';
    public $timestamps = false;
    const DELETED_AT = 'is_deleted';
    protected $fillable = [
    	'id',
        'user_id',
        'name',
        'dob',
        'age',
        'sorts',
        'status',
    ];
   
}
