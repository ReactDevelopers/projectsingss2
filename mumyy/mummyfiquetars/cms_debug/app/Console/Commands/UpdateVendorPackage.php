<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Portfolio\Entities\PortfolioMedia;
use Modules\Vendor\Entities\VendorPlanSubscription;
use Modules\Vendor\Entities\Vendor;
use Carbon\Carbon;

class UpdateVendorPackage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vendor:update:package';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update vendor package';


    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $vendors = Vendor::whereHas('vendorPlanSubscription', function($query){
                        })
                        ->where('status', 1)
                        ->whereNull('is_deleted')
                        ->get();

        if(count($vendors)){
            foreach ($vendors as $key => $vendor) {
                $vendorPlanSubscription = $vendor->vendorPlanSubscription;
                // check expired package
                if(count($vendorPlanSubscription)){
                    foreach ($vendorPlanSubscription as $item) {
                        $now = Carbon::now();
                        if($item->ends_at < $now && $item->plan_id != 2){
                            $item->canceled_at = $now;
                            $item->updated_at = $now;
                            $item->save();
                        }
                    }
                }
                
                // add free package if no package exist
                $vendorPlanSubscription = $vendor->vendorPlanSubscription->where('canceled_at', null);
                if(!count($vendorPlanSubscription)){
                    $vendorFreePlanSubscription = VendorPlanSubscription::where('plan_id', 2)
                                                                        ->where('user_id', $vendor->id)
                                                                        ->orderBy('id', 'desc')
                                                                        ->first();
                    $city_id                    = count($vendorFreePlanSubscription) ? $vendorFreePlanSubscription->city_id : "";
                    $now                        = Carbon::now();
                    $newItem                    = new VendorPlanSubscription();
                    $newItem->user_id           = $vendor->id;
                    $newItem->plan_id           = 2;
                    $newItem->city_id           = $city_id;
                    $newItem->name              = "main";
                    $newItem->trial_ends_at     = null;
                    $newItem->starts_at         = $now;
                    $newItem->ends_at           = $now;
                    $newItem->canceled_at       = null;
                    $newItem->created_at        = $now;
                    $newItem->updated_at        = $now;
                    $newItem->save();
                }
            }
        }
        echo "done!!!";
        echo "\r\n";
    }
}
