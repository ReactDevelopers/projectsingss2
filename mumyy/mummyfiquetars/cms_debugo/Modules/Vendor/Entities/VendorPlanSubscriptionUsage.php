<?php namespace Modules\Vendor\Entities;

use Illuminate\Database\Eloquent\Model;
use Gerardojbaez\LaraPlans\Models\PlanSubscriptionUsage;

class VendorPlanSubscriptionUsage extends PlanSubscriptionUsage
{
    protected $table = 'mm__plan_subscription_usages';
}
