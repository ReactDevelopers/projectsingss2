<?php namespace Modules\Vendor\Entities;

use Illuminate\Database\Eloquent\Model;
use Gerardojbaez\LaraPlans\Models\PlanSubscription;

class VendorPlanSubscription extends PlanSubscription
{
    protected $table = 'mm__plan_subscriptions';

    public function vendorPlanSubscription()
    {
        return $this->belongsTo('Modules\Vendor\Entities\VendorPlan', 'plan_id');
    }

    public function vendor()
    {
        return $this->belongsTo('Modules\Vendor\Entities\Vendor', 'user_id');
    }

    public function getPlanName(){
    	return $this->vendorPlanSubscription ? $this->vendorPlanSubscription->name : "";
    }
}
