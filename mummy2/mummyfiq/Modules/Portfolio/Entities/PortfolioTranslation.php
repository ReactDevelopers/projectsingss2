<?php namespace Modules\Portfolio\Entities;

use Illuminate\Database\Eloquent\Model;

class PortfolioTranslation extends Model
{
    public $timestamps = false;
    protected $fillable = [];
    protected $table = 'portfolio__portfolio_translations';
}
