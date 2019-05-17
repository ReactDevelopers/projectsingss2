<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Portfolio\Entities\PortfolioMedia;
use Modules\Vendor\Entities\User;
use Modules\Vendor\Entities\Vendor;
use Modules\Vendor\Entities\VendorPlan;
use Modules\Vendor\Entities\VendorPhone;
use Modules\Vendor\Services\VendorService;
use Modules\Vendor\Repositories\VendorPhoneRepository;

class UpdateVendorPlan extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vendor:update:plan';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update vendor plan';

    /**
     * @var vendorService
     */
    private $vendorService;

    /**
     * @var VendorPhoneRepository
     */
    private $vendorPhoneRepository;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(vendorService $vendorService, VendorPhoneRepository $vendorPhoneRepository)
    {
        parent::__construct();
        $this->vendorService = $vendorService;
        $this->vendorPhoneRepository = $vendorPhoneRepository;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $vendors = $this->vendorService->all();

        // creating subscriptions
        $users = User::join('role_users', 'role_users.user_id', '=', 'users.id')->where('role_users.role_id', Config('constant.user_role.vendor'))->whereNull('is_deleted')->get();
        $plan = VendorPlan::where('name', 'Free')->first();
        if(count($users)){
            foreach ($users as $key => $user) {
                if(!$user->vendorPlanSubscription){
                    $user->newSubscription('main', $plan)->create();
                    echo $user->id;
                    echo "\r\n";
                }
            }
        }

        echo "done!!!";
        echo "\r\n";
    }
}
