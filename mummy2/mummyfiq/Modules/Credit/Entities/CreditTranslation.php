<?php namespace Modules\Credit\Entities;

use Illuminate\Database\Eloquent\Model;

class CreditTranslation extends Model
{
    public $timestamps = false;
    protected $fillable = [];
    protected $table = 'credit__credit_translations';
}
