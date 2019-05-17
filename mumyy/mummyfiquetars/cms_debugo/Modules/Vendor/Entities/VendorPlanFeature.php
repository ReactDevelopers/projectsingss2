<?php namespace Modules\Vendor\Entities;

use Illuminate\Database\Eloquent\Model;
use Gerardojbaez\LaraPlans\Models\PlanFeature;

class VendorPlanFeature extends PlanFeature
{
    protected $table = 'mm__plan_features';
}
