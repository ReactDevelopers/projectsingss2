<?php namespace App\Mummy\Api\V1\Entities;

use Illuminate\Database\Eloquent\Model;
class ResetPasswordCustomer extends Model
{
    protected $table = 'password_resets';
    public $timestamps = false;
    protected $fillable = [
    	'id',
        'token',
        'email',
        'created_at',
    ];
   
}