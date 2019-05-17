<?php

namespace App\Mummy\Api\V1\Entities\Vendors;

use Illuminate\Database\Eloquent\Model;

class VendorPortfolioMedia extends Model
{
     protected $table = 'mm__vendors_portfolio_media';
     public $timestamps = false;
    protected $fillable = ['id','portfolio_id','media_url','status'];
     public function vendorPortfolio()
    {
        return $this->belongsTo('App\Mummy\Api\V1\Entities\Vendors\VendorPortfolio', 'id');
    }
}
