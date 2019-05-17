<?php

namespace App\Mummy\Api\V1\Entities;

use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class Customer extends Model
{
	use HasApiTokens;
	use Notifiable;
     protected $table = 'users';
    protected $fillable = ['id','email','password','address','name','created_at','updated_at','country_name'];

}
