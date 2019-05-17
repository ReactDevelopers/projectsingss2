<?php namespace Modules\Vendor\Entities;

use Illuminate\Database\Eloquent\Model;
use Gerardojbaez\LaraPlans\Models\Plan;

class VendorPlan extends Plan
{
    protected $table = 'mm__plans';
}
