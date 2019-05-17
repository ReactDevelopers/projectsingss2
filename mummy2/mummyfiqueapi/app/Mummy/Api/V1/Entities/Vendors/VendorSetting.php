<?php

namespace App\Mummy\Api\V1\Entities\Vendors;

use Illuminate\Database\Eloquent\Model;

class VendorSetting extends Model
{

    protected $fillable = ['id','vendor_id','profile_report_leads','someone_left_a_review','addition_emails'];
    public $timestamps = false;
    protected $table = 'mm__vendors_settings';

}
