<?php 

namespace App\Mummy\Api\V1\Entities;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['name', 'value', 'description', 'isTranslatable', 'plainValue'];
    protected $table = 'setting__settings';
}
